<template>
    <div>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0" round color="success" @click="addItem">Add new</v-btn>
        <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search account name, serial number or description" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['description', 'serial number', 'account name', 'issued']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>

            </v-card-text>
        </v-card>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{props.index + 1}}</td>
                <td>{{ props.item.account_name }}</td>
                <td>{{ props.item.serial_number }}</td>
                <td>{{ props.item.description }}</td>
                <td>{{ props.item.specs }}</td>
                <td>{{ moment(props.item.date_issued).format('YYYY, MMM DD') }}</td>
                <td>
                    <v-btn icon small @click="editItem(props.item)">
                        <v-icon small>edit</v-icon>
                    </v-btn>
                    <v-btn icon small @click="deleteItem(props.item)" :loading="props.item.isDeleting">
                        <v-icon small>delete</v-icon>
                    </v-btn>
                </td>
            </template>
            <template slot="footer">
                <td colspan="100%" class="caption grey--text font-italic text-xs-center">
                    <strong>Showing {{items.length}} out of {{total}} result</strong>
                </td>
            </template>
        </v-data-table>
        <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
        <add-edit-item v-model="oepnAddEdit" :fixedAsset="activeItem" @save="updateList" />
    </div>
</template>

<script>
import AddEditItem from './AddEditItem.vue';

export default {
    components: {
        AddEditItem
    },
    data() {
        return {
            total: 0,
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'issued',
            orderBy: 'desc',
            cancelSource: null,
            items: [],
            loading: false,
            reset: true,
            oepnAddEdit: false,
            openItems: false,
            activeItem: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Account name',
                    sortable: false
                },
                {
                    text: 'Serial Number',
                    sortable: false
                },
                {
                    text: 'Description',
                    sortable: false
                },
                {
                    text: 'Specs',
                    sortable: false
                },
                {
                    text: 'Issued',
                    sortable: false
                },
                {
                    text: '',
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
            axios.get('/api/fixed-assets/items', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy,
                    date: this.date
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.items = res.data.result.data;
                    this.reset = false;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
                this.total = res.data.result.total;
            }).finally(() => {
                this.loading = false;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page+= 1;
            this.load();
        },
        addItem() {
            this.activeItem = null;
            this.oepnAddEdit = true;
        },
        editItem(item) {
            this.activeItem = item;
            this.oepnAddEdit = true;
        },
        deleteItem(item) {
            if(confirm("Delete this item?")) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('fixedAsset/deleteItem', item.serial_number).then((res, rej) => {
                    this.items = this.items.filter(i => i.serial_number != item.serial_number);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        },
        updateList(data) {
            if(data.mode == 'insert') {
                this.activeItem = data.fixedAsset;
                this.items.push(data.fixedAsset);
            } else {
                this.activeItem.account_name = data.fixedAsset.account_name;
                this.activeItem.serial_number = data.fixedAsset.serial_number;
                this.activeItem.description = data.fixedAsset.description;
                this.activeItem.specs = data.fixedAsset.specs;
                this.activeItem.date_issued = data.fixedAsset.date_issued;
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
