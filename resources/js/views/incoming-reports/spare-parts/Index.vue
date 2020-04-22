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
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['received_date', 'po_number', 'rr_number', 'pi_number']" @change="filter"></v-combobox>
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
                <td>{{ moment(props.item.received_date).format('YYYY, MMM DD') }}</td>
                <td>{{ props.item.tracking_number }}</td>
                <td>{{ props.item.rr_number }}</td>
                <td>{{ props.item.pi_number }}</td>
                <td>{{ props.item.order_number }}</td>
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
            sortBy: 'received_date',
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
                    text: 'Date received',
                    sortable: false
                },
                {
                    text: 'Tracking Number',
                    sortable: false
                },
                {
                    text: 'RR Number',
                    sortable: false
                },
                {
                    text: 'PI Number',
                    sortable: false
                },
                {
                    text: 'Order number',
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
            axios.get('/api/incoming-reports/spare-parts', {
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
                this.$store.dispatch('incomingSparePartReport/deleteReport', report.id).then((res, rej) => {
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
