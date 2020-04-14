<template>
    <v-card flat transparent>
        <v-divider></v-divider>
        <v-card-actions>
            <v-btn small flat @click="prev">
                <v-icon left>chevron_left</v-icon>
                {{moment(value).add(-1, 'days').format('(D) ddd')}}
            </v-btn>
            <v-spacer></v-spacer>
            <div>
                <v-btn small flat @click="browseDate">
                    {{moment(value).format('MMMM DD, YYYY')}}
                    <v-icon small right>event</v-icon>
                </v-btn>
            </div>
            <v-spacer></v-spacer>
            <v-btn small flat @click="next">
                {{moment(value).add(1, 'days').format('ddd (D)')}}
                <v-icon right>chevron_right</v-icon>
            </v-btn>
        </v-card-actions>
        <v-divider></v-divider>
        <v-dialog v-model="openDateDialog" max-width="290px">
            <v-card>
                <v-date-picker v-model="date" @input="select"></v-date-picker>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
export default {
    props: ['value'],
    data() {
        return {
            openDateDialog: false,
            date: this.value
        }
    },
    methods: {
        select(data) {
            this.$emit('input', data);
            this.openDateDialog = false;
        },
        browseDate() {
            this.openDateDialog = true;
        },
        next() {
            this.$emit('input', moment(this.value).add(1, 'days').format('YYYY-MM-DD'));
        },
        prev() {
            this.$emit('input', moment(this.value).add(-1, 'days').format('YYYY-MM-DD'));
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.date = val;
            }
        }
    }
}
</script>
