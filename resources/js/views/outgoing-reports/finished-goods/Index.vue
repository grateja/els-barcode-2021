<template>
    <div>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0" round color="success" @click="addReport">Add new report</v-btn>
        <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search reference number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['owner_name', 'subdealer_name', 'date_delivered', 'po_date', 'downpayment_date', 'invoice_date', 'quotation_number', 'sales_invoice', 'dr_number', 'warranty_number']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>

            </v-card-text>
        </v-card>

        <div style="overflow: auto">
            <div style="width: 1920px">
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{props.index + 1}}</td>
                        <td>{{ props.item.owner_name }}</td>
                        <td>{{ props.item.subdealer_name }}</td>
                        <td>{{ props.item.reference_number }}</td>
                        <td>{{ moment(props.item.date_delivered).format('YYYY, MMM DD') }}</td>
                        <td>{{ moment(props.item.po_date).format('YYYY, MMM DD') }}</td>
                        <td>{{ moment(props.item.downpayment_date).format('YYYY, MMM DD') }}</td>
                        <td>{{ moment(props.item.invoice_date).format('YYYY, MMM DD') }}</td>
                        <td>{{ props.item.quotation_number }}</td>
                        <td>{{ props.item.sales_invoice }}</td>
                        <td>{{ props.item.dr_number }}</td>
                        <td>{{ props.item.warranty_number }}</td>
                        <td>{{ props.item.truck }}</td>
                        <td>{{ props.item.driver }}</td>
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
                </v-data-table>
            </div>
        </div>
        <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
        <report-dialog v-model="openReport" :report="activeReport" @save="updateList" />
        <items-dialog v-model="openItems" :report="activeReport" />
    </div>
</template>

<script>
import ReportDialog from './ReportDialog.vue';
import ItemsDialog from './ItemsDialog.vue';

export default {
    components: {
        ReportDialog,
        ItemsDialog
    },
    data() {
        return {
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'invoice_date',
            orderBy: 'desc',
            cancelSource: null,
            items: [],
            loading: false,
            reset: true,
            openReport: false,
            openItems: false,
            activeReport: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Client name',
                    sortable: false
                },
                {
                    text: 'Subdealer name',
                    sortable: false
                },
                {
                    text: 'Reservation',
                    sortable: false
                },
                {
                    text: 'Date delivered',
                    sortable: false
                },
                {
                    text: 'PO Date',
                    sortable: false
                },
                {
                    text: 'Downpayment date',
                    sortable: false
                },
                {
                    text: 'Invoice date',
                    sortable: false
                },
                {
                    text: 'Quotation number',
                    sortable: false
                },
                {
                    text: 'Sales invoice',
                    sortable: false
                },
                {
                    text: 'DR Number',
                    sortable: false
                },
                {
                    text: 'Warranty number',
                    sortable: false
                },
                {
                    text: 'Truck',
                    sortable: false
                },
                {
                    text: 'Driver',
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
            axios.get('/api/outgoing-reports/finished-goods', {
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
        addReport() {
            this.activeReport = null;
            this.openReport = true;
        },
        editItem(report) {
            this.activeReport = report;
            this.openReport = true;
        },
        viewItems(report) {
            this.activeReport = report;
            this.openItems = true;
        },
        deleteItem(report) {
            if(confirm("Delete this report?")) {
                Vue.set(report, 'isDeleting', true);
                this.$store.dispatch('outgoingFinishedGoodReport/deleteReport', report.id).then((res, rej) => {
                    this.items = this.items.filter(c => c.id != report.id);
                }).finally(() => {
                    Vue.set(report, 'isDeleting', false);
                })
            }
        },
        updateList(data) {
            if(data.mode == 'insert') {
                this.activeReport = data.report;
                this.items.push(data.report);
            } else {
                this.activeReport.received_date = data.report.received_date;
                this.activeReport.po_number = data.report.po_number;
                this.activeReport.rr_number = data.report.rr_number;
                this.activeReport.pi_number = data.report.pi_number;
                this.activeReport.truck_number = data.report.truck_number;
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
