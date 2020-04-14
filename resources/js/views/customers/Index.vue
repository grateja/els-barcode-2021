<template>
    <v-container>
        <h4 class="title grey--text">Customers</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn class="primary ml-0" round @click="addCustomer">
            <v-icon small left>add</v-icon>
            Create new customer
        </v-btn>
        <form @submit.prevent="filter">
            <v-text-field outline v-model="keyword" label="Search" append-icon="search" @input="filter"></v-text-field>
        </form>


        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{props.index + 1}}</td>
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.contact_number }}</td>
                <td>{{ props.item.email }}</td>
                <td>{{ props.item.address }}</td>
                <td>{{ date(props.item.first_visit) }}</td>
                <td>{{props.item.customer_washes_count}}</td>
                <td>{{props.item.customer_dries_count}}</td>
                <td>
                    <span v-if="props.item.earned_points">
                        {{props.item.earned_points.toFixed(2)}}
                    </span>
                </td>
                <td>
                    <v-tooltip top>
                        <v-btn slot="activator" small icon @click="editCustomer(props.item)">
                            <v-icon small>edit</v-icon>
                        </v-btn>
                        <span>Edit details</span>
                    </v-tooltip>
                    <v-tooltip top v-if="isOwner">
                        <v-btn slot="activator" small icon @click="deleteCustomer(props.item)" :loading="props.item.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                        <span>Edit details</span>
                    </v-tooltip>
                    <v-tooltip top v-if="props.item.rfid_cards_count > 0">
                        <v-btn slot="activator" small icon @click="viewCards">
                            <v-icon small>credit_card</v-icon>
                            {{props.item.rfid_cards_count}}
                        </v-btn>
                        <span>{{props.item.rfid_cards_count}} Registered RFID card(s)</span>
                    </v-tooltip>
                </td>
            </template>
            <template slot="footer">
                <tr v-if="!!summary">
                    <td colspan="10">
                        <div class="font-italic grey--text">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{summary.total_items}}</span> result(s)</div>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
        <customer-dialog v-model="openCustomerDialog" :customer="activeCustomer" @save="editContinue"></customer-dialog>
    </v-container>
</template>

<script>
import CustomerDialog from './CustomerDialog.vue';
import moment from 'moment';

export default {
    components: {
        CustomerDialog
    },
    data() {
        return {
            reset: true,
            cancelSource: null,
            keyword: this.$route.query.keyword,
            page: parseInt(this.$route.query.page) || 1,
            loading: false,
            totalPage: 0,
            items: [],
            summary: null,
            activeCustomer: null,
            openCustomerDialog: false,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Contact No.',
                    sortable: false
                },
                {
                    text: 'Email',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: 'First visit',
                    sortable: false
                },
                {
                    text: 'Available wash',
                    sortable: false
                },
                {
                    text: 'Available dry',
                    sortable: false
                },
                {
                    text: 'Earned points',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.reset = true;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            this.loading = true;
            axios.get('/api/customers', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
                this.summary = res.data.summary;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        editCustomer(customer) {
            this.activeCustomer = customer;
            this.openCustomerDialog = true;
        },
        editContinue(data) {
            if(data.mode == 'insert') {
                this.items.push(data.customer);
            } else {
                this.activeCustomer.name = data.customer.name;
                this.activeCustomer.contact_number = data.customer.contact_number;
                this.activeCustomer.email = data.customer.email;
                this.activeCustomer.address = data.customer.address;
                this.activeCustomer.first_visit = data.customer.first_visit;
            }
        },
        addCustomer() {
            this.activeCustomer = null;
            this.openCustomerDialog = true;
        },
        date(date) {
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.reset = false;
            this.load();
        },
        deleteCustomer(customer) {
            if(confirm('Delete this customer?')) {
                Vue.set(customer, 'isDeleting', true);
                this.$store.dispatch('customer/deleteCustomer', customer.id).then((res, rej) => {
                    this.items = this.items.filter(c => c.id != customer.id);
                }).finally(() => {
                    Vue.set(customer, 'isDeleting', false);
                })
            }
        },
        viewCards(customer) {

        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
