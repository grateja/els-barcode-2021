<template>
    <v-container>
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
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['downpayment_date', 'reference_number', 'owner_name', 'subdealer_name']" @change="filter"></v-combobox>
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
                <td>{{ moment(props.item.downpayment_date).format('YYYY, MMM DD') }}</td>
                <td>{{ props.item.reference_number }}</td>
                <td>{{ props.item.owner_name }}</td>
                <td>{{ props.item.subdealer_name }}</td>
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
        <items-dialog v-model="openItems" :report="activeReport" />
        <report-dialog v-model="openReport" :report="activeReport" @save="updateList" />
    </v-container>
</template>

<script>
import ItemsDialog from './ItemsDialog.vue';
import ReportDialog from './ReportDialog.vue';

export default {
    components: {
        ItemsDialog,
        ReportDialog
    },
    data() {
        return {
            total: 0,
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'downpayment_date',
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
                    text: 'Downpayment date',
                    sortable: false
                },
                {
                    text: 'Reference number',
                    sortable: false
                },
                {
                    text: 'Client',
                    sortable: false
                },
                {
                    text: 'Subdealer',
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
            axios.get('/api/reservations', {
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
                this.$store.dispatch('reservation/deleteReport', report.id).then((res, rej) => {
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
                this.activeReport.downpayment_date = data.report.downpayment_date;
                this.activeReport.reference_number = data.report.reference_number;
                this.activeReport.owner_name = data.report.owner_name;
                this.activeReport.subdealer_name = data.report.subdealer_name;
                this.activeReport.remarks = data.report.remarks;
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
