<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Incoming report</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.receivedDate" :error-messages="errors.get('receivedDate')" outline label="Received date" ref="receivedDate" type="date"></v-text-field>
                    <v-text-field v-model="formData.trackingNumber" :error-messages="errors.get('trackingNumber')" outline label="Tracking number"></v-text-field>
                    <v-text-field v-model="formData.rrNumber" :error-messages="errors.get('rrNumber')" outline label="RR Number"></v-text-field>
                    <v-text-field v-model="formData.piNumber" :error-messages="errors.get('piNumber')" outline label="PI Number"></v-text-field>
                    <v-text-field v-model="formData.orderNumber" :error-messages="errors.get('orderNumber')" outline label="Order Number"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'report'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                receivedDate: moment().format('YYYY-MM-DD'),
                orderNumber: null,
                rrNumber: null,
                piNumber: null,
                trackingNumber: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`incomingSparePartReport/${this.mode}Report`, {
                reportId: this.report ? this.report.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    report: res.data.report,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['incomingSparePartReport/getErrors'];
        },
        saving() {
            return this.$store.getters['incomingSparePartReport/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.report) {
                this.mode = 'update';
                this.formData.receivedDate = moment(this.report.received_date).format('YYYY-MM-DD');
                this.formData.orderNumber = this.report.order_number;
                this.formData.rrNumber = this.report.rr_number;
                this.formData.piNumber = this.report.pi_number;
                this.formData.trackingNumber = this.report.tracking_number;
            } else {
                this.mode = 'insert';
                this.formData.receivedDate = moment().format('YYYY-MM-DD');
                this.formData.orderNumber = null;
                this.formData.rrNumber = null;
                this.formData.piNumber = null;
                this.formData.trackingNumber = null;
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
