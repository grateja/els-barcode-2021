<template>
    <v-dialog :value="value" max-width="700" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Incoming report</v-card-title>
                <v-divider></v-divider>
                <v-layout row wrap>
                    <v-flex xs12 sm5>
                        <v-card-text>
                            <template v-if="!!client">
                                <v-text-field label="Client" :value="client.owner_name" v-if="client" readonly clearable @click:clear="client = null"></v-text-field>
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
                                <v-text-field label="Client" :value="subdealer.subdealer_name" v-if="subdealer" readonly clearable @click:clear="subdealer = null"></v-text-field>
                            </template>
                            <template v-else>
                                <v-btn round small :outline="!subdealer" class="ml-0 mb-0" @click="browseSubdealer = true" :color="!!subdealer? 'success' : ''">
                                    <v-icon left>search</v-icon>
                                    Browse subdealer</v-btn>
                                <v-btn round small :outline="!subdealer" class="ml-0 mb-0" @click="createSubdealer = true" :color="!!subdealer? 'success' : ''">
                                    <v-icon left>add</v-icon>
                                    Create subdealer</v-btn>
                            </template>
                        </v-card-text>
                    </v-flex>
                    <v-flex xs12 sm7>
                        <v-card-text>
                            <v-text-field v-model="formData.downpaymentDate" :error-messages="errors.get('downpaymentDate')" outline label="Downpayment date" ref="downpaymentDate" type="date"></v-text-field>
                            <v-text-field v-model="formData.referenceNumber" :error-messages="errors.get('referenceNumber')" outline label="Reference Number"></v-text-field>

                            <v-text-field v-model="formData.remarks" :error-messages="errors.get('remarks')" outline label="Remarks"></v-text-field>
                        </v-card-text>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <client-browser v-model="browseClient" @select="selectClient" />
        <client-dialog v-model="createClient" @save="newClient" />
        <subdealer-browser v-model="browseSubdealer" @select="selectSubdealer" />
        <subdealer-dialog v-model="createSubdealer" @save="newSubdealer" />
    </v-dialog>
</template>

<script>
import ClientBrowser from '../shared/ClientBrowser.vue';
import ClientDialog from '../clients/AddEditDialog.vue';
import SubdealerBrowser from '../shared/SubdealerBrowser.vue';
import SubdealerDialog from '../subdealers/AddEditDialog.vue';

export default {
    components: {
        ClientBrowser,
        ClientDialog,
        SubdealerBrowser,
        SubdealerDialog
    },
    props: [
        'value', 'report'
    ],
    data() {
        return {
            browseClient: false,
            createClient: false,
            browseSubdealer: false,
            createSubdealer: false,
            loading: false,
            mode: 'insert',
            client: null,
            subdealer: null,
            formData: {
                downpaymentDate: moment().format('YYYY-MM-DD'),
                referenceNumber: null,
                ownerName: null,
                subdealerName: null,
                remarks: null
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
            this.$store.dispatch(`reservation/${this.mode}Report`, {
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
            if(!!this.report) {
                this.loading = true;

                axios.get(`/api/reservations/${this.report.id}`).then((res, rej) => {
                    this.client = res.data.report.client;
                    this.subdealer = res.data.report.subdealer;
                }).finally(() => {
                    this.loading = false;
                });
            }
        },
        selectClient(client) {
            this.client = client;
        },
        newClient(data) {
            this.client = data.client;
        },
        removeClient() {
            this.client = null;
        },
        selectSubdealer(subdealer) {
            this.subdealer = subdealer;
        },
        newSubdealer(data) {
            this.subdealer = data.subdealer;
        },
        removeSubdealer() {
            this.subdealer = null;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['reservation/getErrors'];
        },
        saving() {
            return this.$store.getters['reservation/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.report) {
                this.mode = 'update';
                this.formData.downpaymentDate = moment(this.report.downpayment_date).format('YYYY-MM-DD');
                this.formData.referenceNumber = this.report.reference_number;
                this.formData.remarks = this.report.remarks;
                this.client = this.report.client;
                this.subdealer = this.report.subdealer;
                this.get();
            } else {
                this.mode = 'insert';
                this.formData.downpaymentDate = moment().format('YYYY-MM-DD');
                this.formData.referenceNumber = null;
                this.formData.remarks = null;
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
