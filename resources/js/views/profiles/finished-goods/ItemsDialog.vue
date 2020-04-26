<template>
    <v-dialog :value="value" persistent max-width="1280">
        <v-card min-height="500px">
            <v-card-title>
                <span class="grey--text">Report items</span>
                <v-spacer></v-spacer>
                <v-btn @click="close" icon small>
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-layout row wrap v-if="finishedGood">
                    <v-flex xs12 sm5 md4>
                        <v-layout row wrap>
                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Model:</v-flex>
                            <v-flex xs6>{{finishedGood.id}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Description:</v-flex>
                            <v-flex xs6>{{finishedGood.description}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Specs:</v-flex>
                            <v-flex xs6>{{finishedGood.specs}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Supplier:</v-flex>
                            <v-flex xs6>{{finishedGood.supplier}}</v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex xs12 sm7 md8>
                        <v-card>
                            <v-text-field outline label="Search serial number" @keyup="filter" v-model="keyword" ref="keyword" />
                            <v-card flat transparent style="overflow-y: auto; max-height: 480px" ref="card" id="card">
                                <v-data-table v-model="selectedItems" item-key="serial_number" select-all :items="items" dense :headers="headers" hide-actions :loading="loading">
                                    <template v-slot:items="props">
                                        <tr :active="props.selected" @click="props.selected = !props.selected">
                                            <td>
                                                <v-checkbox :input-value="props.selected"></v-checkbox>
                                            </td>
                                            <td>{{props.index + 1}}</td>
                                            <td>{{ props.item.serial_number }}</td>
                                            <td>{{ props.item.warehouse }}</td>
                                            <td>{{ props.item.current_location }}</td>
                                            <td>{{ moment(props.item.created_at).format('LLL') }}</td>
                                            <td>
                                                <v-btn icon small @click="edit(props.item, $event)">
                                                    <v-icon small>edit</v-icon>
                                                </v-btn>
                                                <v-btn icon small @click="deleteItem(props.item, $event)">
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
                            </v-card>
                            <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
                            <v-card-actions>
                                <v-btn round color="success" small @click="addItem">
                                    <v-icon left>add</v-icon>
                                    add item
                                </v-btn>

                                <v-btn v-if="selectedItems && selectedItems.length" round small @click="print" :loading="printing">
                                    <v-icon left>print</v-icon>
                                    Print ({{selectedItems.length}})
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
        <serial-dialog v-if="finishedGood" v-model="openAddEdit" :model="finishedGood.id" :finishedGood="activeItem" @save="updateList" />
    </v-dialog>
</template>

<script>
import SerialDialog from '../../finished-goods/SerialDialog.vue';

export default {
    components: {
        SerialDialog
    },
    props: [
        'value', 'finishedGood'
    ],
    data() {
        return {
            printing: false,
            total: 0,
            reset: true,
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'description',
            orderBy: 'desc',
            cancelSource: null,
            loading: false,
            openAddEdit: false,
            activeItem: null,
            items: [],
            selectedItems: [],
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Serial number',
                    sortable: false
                },
                {
                    text: 'Warehouse',
                    sortable: false
                },
                {
                    text: 'Location',
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
        close() {
            this.$emit('input', false);
        },
        filter() {
            this.reset = true;
            this.page = 1;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get(`/api/finished-goods/items/${this.finishedGood.id}`, {
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
                        this.$refs.card.$el.scrollTo({
                            top: this.$refs.card.$el.scrollHeight,
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
        edit(item, e) {
            e.stopPropagation();
            this.activeItem = item;
            this.openAddEdit = true;
        },
        updateList(data) {
            if(data.mode == 'insert') {
                this.activeItem = data.finishedGood;
                this.items.push(data.finishedGood);
            } else {
                this.activeItem.serial_number = data.finishedGood.id;
                this.activeItem.warehouse = data.finishedGood.warehouse;
                this.activeItem.current_location = data.finishedGood.current_location;
            }
        },
        deleteItem(item, e) {
            e.stopPropagation();
            if(confirm('Remvoe this item(s) from this finished good?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('finishedGood/deleteItem', item.serial_number).then((res, rej) => {
                    this.items = this.items.filter(i => i.serial_number != item.serial_number);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
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
    watch: {
        value(val) {
            if(val && this.finishedGood) {
                this.load();
                setTimeout(() => {
                    this.$refs.keyword.$el.querySelector('input').select();
                }, 500);
            } else {
                this.items = [];
                this.reset = true;
            }
        }
    }
}
</script>
