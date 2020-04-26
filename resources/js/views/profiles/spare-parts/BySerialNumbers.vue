<template>
    <div>
        <v-btn class="ml-0" round color="success" @click="addItem">Add new</v-btn>

        <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search serial number, model, description or specs" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['serial_number', 'specs', 'description', 'part_number']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>

            </v-card-text>
        </v-card>

        <v-expand-transition>
            <v-btn v-if="selectedItems.length" round @click="print" :loading="printing">Print ({{selectedItems.length}})</v-btn>
        </v-expand-transition>
        <v-data-table :headers="headers" :items="items" item-key="serial_number" :loading="loading" hide-actions select-all v-model="selectedItems">
            <template v-slot:items="props">
                <tr :active="props.selected" @click="props.selected = !props.selected">
                    <td><v-checkbox :input-value="props.selected" :label="`${props.index + 1}`"></v-checkbox></td>
                    <td>{{ props.item.serial_number }}</td>
                    <td>{{ props.item.part_number }}</td>
                    <td>{{ props.item.description }}</td>
                    <td>{{ props.item.specs }}</td>
                    <td>{{ props.item.supplier }}</td>
                    <td>{{ moment(props.item.created_at).format('LLL') }}</td>
                    <td>
                        <v-btn icon small @click="editItem(props.item, $event)">
                            <v-icon small>edit</v-icon>
                        </v-btn>
                        <v-btn icon small @click="deleteItem(props.item, $event)" :loading="props.item.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
            <template slot="footer">
                <td colspan="100%" class="caption grey--text font-italic text-xs-center">
                    <strong>Showing {{items.length}} out of {{total}} result</strong>
                </td>
            </template>
        </v-data-table>
        <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
        <v-btn v-if="selectedItems.length" round @click="print" :loading="printing">Print ({{selectedItems.length}})</v-btn>
        <add-edit-dialog v-model="openAddEdit" :sparePart="activeItem" @save="updateList" />
    </div>
</template>

<script>
import AddEditDialog from '../../spare-parts/AddEditDialog.vue';

export default {
    components: {
        AddEditDialog
    },
    data() {
        return {
            printing: false,
            total: 0,
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'scanned',
            orderBy: 'desc',
            cancelSource: null,
            items: [],
            loading: false,
            reset: true,
            openAddEdit: false,
            activeItem: null,
            selectedItems: [],
            headers: [
                {
                    text: 'Serial number',
                    sortable: false
                },
                {
                    text: 'Part number',
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
                    text: 'Supplier',
                    sortable: false
                },
                {
                    text: 'Scaned/Created',
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
            axios.get('/api/spare-parts/items', {
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
            this.openAddEdit = true;
        },
        editItem(item, e) {
            e.stopPropagation();
            this.openAddEdit = true;
            this.activeItem = item;
        },
        deleteItem(item, e) {
            e.stopPropagation();
            if(confirm("Are you sure you want to delete this item?")) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('sparePartProfile/deleteSparePartProfile', item.id).then((res, rej) => {
                    this.items = this.items.filter(i => i.id != item.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        },
        updateList(data) {
            console.log(data)
            if(data.mode == 'insert') {
                this.activeItem = data.sparePart;
                this.items.push(data.sparePart);
            } else {
                this.activeItem.serial_number = data.sparePart.serial_number;
                this.activeItem.part_number = data.sparePart.part_number;
                this.activeItem.description = data.sparePart.description;
                this.activeItem.specs = data.sparePart.specs;
                this.activeItem.supplier = data.sparePart.supplier;
            }
        },
        print() {
            this.printing = true;
            let serialNumbers = this.selectedItems.map(i => i.serial_number);
            this.$store.dispatch('printer/printSerialNumbers', {
                params: {
                    serialNumbers
                }
            }).finally(() => {
                this.printing = false;
            })
        }
    },
    created() {
        this.load();
    }
}
</script>
