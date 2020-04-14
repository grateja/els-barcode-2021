<template>
    <div>
        <div>
            <span v-if="!!value" class="grey--text font-italic">
                Showing results from <span class="font-weight-bold">{{date(value.from)}}</span> to <span class="font-weight-bold">{{date(value.to)}}</span>
            </span>

            <v-tooltip top>
                <v-btn slot="activator" @click="open" icon><v-icon>event</v-icon></v-btn>
                <span>Specify date</span>
            </v-tooltip>
            <v-tooltip top v-if="!!value">
                <v-btn slot="activator" @click="clear" icon><v-icon>clear</v-icon></v-btn>
                <span>Clear date</span>
            </v-tooltip>
        </div>
        <v-dialog :value="openDialog" max-width="480px" persistent>
            <v-card>
                <v-card-title class="title grey--text">Select date range</v-card-title>
                <v-card-text>
                    <v-text-field type="date" v-model="dateFrom" label="Date from :"></v-text-field>
                    <v-text-field type="date" v-model="dateTo" label="Date to :"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="cancel">Cancel</v-btn>
                    <v-btn @click="ok" class="primary">ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    props: [
        'value'
    ],
    data() {
        return {
            openDialog: false,
            dateFrom: new Date().toISOString().substring(0, 10),
            dateTo: null //new Date().toISOString().substring(0, 10)
        }
    },
    methods: {
        ok() {
            let from = moment(this.dateFrom);
            let to = moment(this.dateTo);

            if(!to.isValid()) {
                this.dateTo = this.dateFrom;
            }
            this.$emit('input', {
                from: this.dateFrom,
                to: this.dateTo
            });
            this.openDialog = false;
        },
        cancel() {
            this.openDialog = false;
        },
        open() {
            this.openDialog = true;
        },
        clear() {
            this.$emit('input', null);
        },
        date(date) {
            return moment(date).format('LL');
        }
    },
    watch: {
        value(val) {
            if(val) {
                if(this.value.from) {
                    this.dateFrom = this.value.from;
                }
                if(this.value.to) {
                    this.dateTo = this.value.to;
                }
            } else {
                this.dateFrom = new Date().toISOString().substring(0, 10);
                this.dateTo = new Date().toISOString().substring(0, 10);
            }
        }
    }
}
</script>
