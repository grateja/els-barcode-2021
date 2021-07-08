<template>
    <div>
        <v-btn class="ml-0" round color="success" @click="addItem">Add new</v-btn>

        <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search name" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
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
                <td>{{ props.index + 1 }}</td>
                <td>{{ props.item.employee_id }}</td>
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.department }}</td>
                <td>{{ props.item.fixed_assets_count }}</td>
                <td>
                    <v-btn icon small @click="viewItems(props.item)">
                        <v-icon small>list</v-icon>
                    </v-btn>
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
        <add-edit-item v-model="openAddEdit" :account="activeItem" @save="updateList" />
        <items-dialog v-model="openItems" :account="activeItem" />
    </div>
</template>

<script>
import AddEditItem from './AddEditItem.vue';
import ItemsDialog from './ItemsDialog.vue';

export default {
    components: {
        AddEditItem,
        ItemsDialog
    },
    data() {
        return {
            total: 0,
            keyword: null,
            page: 1,
            sortBy: 'name',
            orderBy: 'desc',
            cancelSource: null,
            items: [],
            loading: false,
            reset: true,
            openAddEdit: false,
            openItems: false,
            activeItem: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Employee ID',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Department',
                    sortable: false
                },
                {
                    text: 'Issued items',
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
            this.reset = true;
            this.page = 1;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get('/api/fixed-assets/accounts', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy
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
            this.openAddEdit = true;
        },
        editItem(item) {
            this.openAddEdit = true;
            this.activeItem = item;
        },
        deleteItem(item) {
            if(confirm("Are you sure you want to delete this item?")) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('fixedAssetAccount/deleteAccount', item.id).then((res, rej) => {
                    this.items = this.items.filter(i => i.id != item.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        },
        updateList(data) {
            console.log(data)
            if(data.mode == 'insert') {
                this.activeItem = data.account;
                this.items.push(data.account);
            } else {
                this.activeItem.employee_id = data.account.employee_id;
                this.activeItem.name = data.account.name;
                this.activeItem.department = data.account.department;
            }
        },
        viewItems(item) {
            this.activeItem = item;
            this.openItems = true;
        }
    },
    created() {
        this.load();
    }
}
</script>
