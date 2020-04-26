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
            <v-progress-linear v-if="loading" height="3" indeterminate class="my-0"></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-card-text>
                <v-layout row wrap v-if="report">
                    <v-flex xs12 sm5 md4>
                        <v-layout row wrap>
                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Client Name:</v-flex>
                            <v-flex xs6>{{report.owner_name}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Subdealer Name:</v-flex>
                            <v-flex xs6>{{report.subdealer_name}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Reservation:</v-flex>
                            <v-flex xs6>{{report.reference_number}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Date Delivered:</v-flex>
                            <v-flex xs6>{{moment(report.date_delivered).format('YYYY, MMM, DD')}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">PO Date:</v-flex>
                            <v-flex xs6>{{moment(report.po_date).format('YYYY, MMM, DD')}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Downpayment Date:</v-flex>
                            <v-flex xs6>{{moment(report.downpayment_date).format('YYYY, MMM, DD')}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Invoice Date:</v-flex>
                            <v-flex xs6>{{moment(report.invoice_date).format('YYYY, MMM, DD')}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Quotation Number:</v-flex>
                            <v-flex xs6>{{report.quotation_number}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Sales Invoice:</v-flex>
                            <v-flex xs6>{{report.sales_invoice}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">DR Number:</v-flex>
                            <v-flex xs6>{{report.dr_number}}</v-flex>

                        </v-layout>
                    </v-flex>
                    <v-flex xs12 sm7 md8>
                        <v-card height="100%">
                            <div style="overflow-y: auto; max-height: 630px" ref="card" id="card">
                                <v-data-table v-model="selectedItems" item-key="serial_number" select-all :items="items" :headers="headers" :loading="loading">
                                    <template v-slot:items="props">
                                        <tr :active="props.selected" @click="props.selected = !props.selected">
                                            <td>
                                                <v-checkbox :input-value="props.selected"></v-checkbox>
                                            </td>
                                            <td>{{props.index + 1}}</td>
                                            <td>{{ props.item.serial_number }}</td>
                                            <td>{{ props.item.model }}</td>
                                            <td>{{ props.item.description }}</td>
                                            <td>{{ props.item.specs }}</td>
                                            <td>{{ props.item.supplier }}</td>
                                        </tr>
                                    </template>
                                </v-data-table>
                            </div>
                            <v-card-actions>
                                <v-btn round color="success" small @click="addItem">
                                    <v-icon left>add</v-icon>
                                    add item
                                </v-btn>

                                <v-btn v-if="selectedItems && selectedItems.length" round small @click="print" :loading="printing">
                                    <v-icon left>print</v-icon>
                                    Print ({{selectedItems.length}})
                                </v-btn>

                                <v-btn v-if="selectedItems && selectedItems.length" round small @click="removeItems" :loading="removingItems">
                                    <v-icon left>clear</v-icon>
                                    Remove ({{selectedItems.length}})
                                </v-btn>

                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
        <add-item-dialog v-if="report" v-model="openAddItem" @selectItems="updateItems" :reportId="report.id" />
    </v-dialog>
</template>

<script>
import AddItemDialog from './AddItemDialog.vue';

export default {
    components: {
        AddItemDialog
    },
    props: [
        'value', 'report'
    ],
    data() {
        return {
            printing: false,
            loading: false,
            openAddItem: false,
            removingItems: false,
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
                    text: 'Model',
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
                }
            ]
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.loading = true;
            axios.get(`/api/outgoing-reports/spare-parts/${this.report.id}/view-items`).then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        addItem() {
            this.openAddItem = true;
        },
        updateItems(data) {
            if(data.items) {
                this.items = [...this.items, ...data.items];
            }
        },
        removeItems() {
            if(this.selectedItems.length < 1) {
                alert('No items selected');
                return;
            }
            if(confirm('Remvoe this item(s) from this report?')) {
                this.removingItems = true;
                this.$store.dispatch('outgoingSparePartReport/removeItems', {
                    reportId: this.report.id,
                    formData: {
                        serialNumbers: this.selectedItems.map(i => i.serial_number)
                    }
                }).then((res, rej) => {
                    this.items = this.items.filter(i => res.data.serialNumbers.indexOf(i.serial_number) < 0);
                }).finally(() => {
                    this.removingItems = false;
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
            if(val && this.report) {
                this.load();
            }
        }
    }
}
</script>
