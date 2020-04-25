<template>
    <v-dialog :value="value" max-width="580" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Outgoing report</v-card-title>
                <v-layout row wrap>
                    <v-flex xs12 sm5>
                        <v-card-text>
                            <template v-if="!!client">
                                <v-text-field label="Client" :value="client.owner_name + ' - ' + client.shop_name" v-if="client" readonly clearable @click:clear="client = null"></v-text-field>
                            </template>
                            <template v-else>
                                <v-btn round small :outline="!client" class="ml-0 mb-0" @click="browseClient = true" :color="!!client? 'success' : ''">
                                    <v-icon left>search</v-icon>
                                    Browse client</v-btn>
                                <v-btn round small :outline="!client" class="ml-0 mb-0" @click="createClient = true" :color="!!client? 'success' : ''">
                                    <v-icon left>add</v-icon>
                                    Create client</v-btn>
                            </template>

                            <v-divider class="my-4"></v-divider>

                            <template v-if="!!subdealer">
                                <v-text-field label="Subdealer" :value="subdealer.subdealer_name + ' - ' + subdealer.company_name" v-if="subdealer" readonly clearable @click:clear="subdealer = null"></v-text-field>
                            </template>
                            <template v-else>
                                <v-btn round small :outline="!subdealer" class="ml-0 mb-0" @click="browseSubdealer = true" :color="!!subdealer? 'success' : ''">
                                    <v-icon left>search</v-icon>
                                    Browse subdealer</v-btn>
                                <v-btn round small :outline="!subdealer" class="ml-0 mb-0" @click="createSubdealer = true" :color="!!subdealer? 'success' : ''">
                                    <v-icon left>add</v-icon>
                                    Create subdealer</v-btn>
                            </template>

                            <v-divider class="my-4"></v-divider>
                            <v-btn round small class="ml-0 mb-0" @click="browseReservation = true" outline>
                                <v-icon left>search</v-icon>
                                Browse reservation</v-btn>
                            <v-text-field label="Reservation" v-model="formData.referenceNumber" :error-messages="errors.get('referenceNumber')" outline></v-text-field>
                        </v-card-text>
                    </v-flex>
                    <v-flex xs12 sm7>
                        <v-card height="630" style="overflow: auto">
                            <v-card-text>
                                <v-text-field v-model="formData.dateDelivered" :error-messages="errors.get('dateDelivered')" outline label="Date Delivered" ref="dateDelivered" type="date"></v-text-field>
                                <v-text-field v-model="formData.poDate" :error-messages="errors.get('poDate')" outline label="PO Date" ref="poDate" type="date"></v-text-field>
                                <v-text-field v-model="formData.downpaymentDate" :error-messages="errors.get('downpaymentDate')" outline label="Downpayment Date" ref="downpaymentDate" type="date"></v-text-field>
                                <v-text-field v-model="formData.invoiceDate" :error-messages="errors.get('invoiceDate')" outline label="Invoice Date" ref="invoiceDate" type="date"></v-text-field>
                                <v-text-field v-model="formData.quotationNumber" :error-messages="errors.get('quotationNumber')" outline label="Quotation Number"></v-text-field>
                                <v-text-field v-model="formData.salesInvoice" :error-messages="errors.get('salesInvoice')" outline label="Sales Invoice"></v-text-field>
                                <v-text-field v-model="formData.drNumber" :error-messages="errors.get('drNumber')" outline label="DR Number"></v-text-field>
                                <v-text-field v-model="formData.warrantyNumber" :error-messages="errors.get('warrantyNumber')" outline label="Warranty number"></v-text-field>
                                <v-text-field v-model="formData.truck" :error-messages="errors.get('truck')" outline label="Truck"></v-text-field>
                                <v-text-field v-model="formData.driver" :error-messages="errors.get('driver')" outline label="Driver"></v-text-field>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <client-browser v-model="browseClient" @select="c => client = c" />
        <client-dialog v-model="createClient" @save="d => client = d.client" />

        <subdealer-browser v-model="browseSubdealer" @select="s => subdealer = s" />
        <subdealer-dialog v-model="createSubdealer" @save="d => subdealer = d.subdealer" />

        <reservation-browser v-model="browseReservation" @select="r => formData.referenceNumber = r.reference_number" />
    </v-dialog>
</template>

<script>
import ClientBrowser from '../../shared/ClientBrowser.vue';
import ClientDialog from '../../clients/AddEditDialog.vue';

import SubdealerBrowser from '../../shared/SubdealerBrowser.vue';
import SubdealerDialog from '../../subdealers/AddEditDialog.vue';

import ReservationBrowser from '../../shared/ReservationBrowser.vue';

export default {
    components: {
        ClientBrowser,
        ClientDialog,
        SubdealerBrowser,
        SubdealerDialog,
        ReservationBrowser
    },
    props: [
        'value', 'report'
    ],
    data() {
        return {
            client: null,
            subdealer: null,
            reservation: null,
            browseClient: false,
            createClient: false,
            browseSubdealer: false,
            createSubdealer: false,
            browseReservation: false,
            mode: 'insert',
            formData: {
                dateDelivered: moment().format('YYYY-MM-DD'),
                poDate: moment().format('YYYY-MM-DD'),
                downpaymentDate: moment().format('YYYY-MM-DD'),
                invoiceDate: moment().format('YYYY-MM-DD'),
                referenceNumber: null,
                quotationNumber: null,
                salesInvoice: null,
                drNumber: null,
                warrantyNumber: null,
                truck: null,
                driver: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.formData.clientId = this.client ? this.client.id : null;
            this.formData.subdealerId = this.subdealer ? this.subdealer.id : null;
            this.$store.dispatch(`outgoingFinishedGoodReport/${this.mode}Report`, {
                reportId: this.report ? this.report.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    report: res.data.report,
                    mode: this.mode
                });
            }).finally(() => {
                this.formData.clientId = null;
                this.formData.subdealerId = null;
            });
        },
        get() {
            axios.get(`/api/outgoing-reports/finished-goods/${this.report.id}`).then((res, rej) => {
                this.subdealer = res.data.report.subdealer;
                this.client = res.data.report.client;
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['outgoingFinishedGoodReport/getErrors'];
        },
        saving() {
            return this.$store.getters['outgoingFinishedGoodReport/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.report) {
                this.mode = 'update';
                this.formData.dateDelivered = moment(this.report.date_delivered).format('YYYY-MM-DD');
                this.formData.poDate = moment(this.report.po_date).format('YYYY-MM-DD');
                this.formData.downpaymentDate = moment(this.report.downpayment_date).format('YYYY-MM-DD');
                this.formData.invoiceDate = moment(this.report.invoice_date).format('YYYY-MM-DD');
                this.formData.quotationNumber = this.report.quotation_number;
                this.formData.salesInvoice = this.report.sales_invoice;
                this.formData.drNumber = this.report.dr_number;
                this.formData.warrantyNumber = this.report.warranty_number;
                this.formData.truck = this.report.truck;
                this.formData.driver = this.report.driver;
                this.formData.referenceNumber = this.report.reference_number;
                this.get();
            } else {
                this.mode = 'insert';
                this.formData.dateDelivered = null;
                this.formData.poDate = null;
                this.formData.downpaymentDate = null;
                this.formData.invoiceDate = null;
                this.formData.quotationNumber = null;
                this.formData.salesInvoice = null;
                this.formData.drNumber = null;
                this.formData.warrantyNumber = null;
                this.formData.truck = null;
                this.formData.driver = null;
                this.formData.referenceNumber = null;
                this.client = null;
                this.subdealer = null;
            }
        },
        report(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
