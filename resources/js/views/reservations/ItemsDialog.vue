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
                <v-layout row wrap v-if="report">
                    <v-flex xs12 sm5 md4>
                        <v-layout row wrap>
                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Downpayment date:</v-flex>
                            <v-flex xs6>{{report.downpayment_date}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Reference Number:</v-flex>
                            <v-flex xs6>{{report.reference_number}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Owner name:</v-flex>
                            <v-flex xs6>{{report.owner_name}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Subdealer name:</v-flex>
                            <v-flex xs6>{{report.subdealer_name}}</v-flex>

                            <v-flex xs6 class="text-xs-right pr-3 caption font-weight-bold grey--text">Remarks:</v-flex>
                            <v-flex xs6>{{report.remarks}}</v-flex>

                        </v-layout>
                    </v-flex>
                    <v-flex xs12 sm7 md8>
                        <v-card class="transparent">
                            <v-card-title class="py-0">
                                <span class="title grey--text">Finished goods</span>
                                <v-spacer></v-spacer>
                                <v-btn icon small @click="showFinishedGoods = !showFinishedGoods">
                                    <v-icon>expand_{{ showFinishedGoods ? 'less' : 'more'}}</v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-expand-transition>
                                <finished-goods v-if="showFinishedGoods && !!report" :reservationId="report.id" />
                            </v-expand-transition>
                        </v-card>
                        <v-card class="transparent">
                            <v-card-title class="py-0">
                                <span class="title grey--text">Spare parts</span>
                                <v-spacer></v-spacer>
                                <v-btn icon small @click="showSpareParts = !showSpareParts">
                                    <v-icon>expand_{{ showSpareParts ? 'less' : 'more'}}</v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-expand-transition>
                                <spare-parts v-if="showSpareParts && !!report" :reservationId="report.id" />
                            </v-expand-transition>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import FinishedGoods from './finished-goods/Items.vue';
import SpareParts from './spare-parts/Items.vue';

export default {
    components: {
        FinishedGoods,
        SpareParts
    },
    props: [
        'value', 'report'
    ],
    data() {
        return {
            urlSource: null,
            showFinishedGoods: true,
            showSpareParts: true,
            openFinishedGood: false,
            openSparePart: false,
            removingFinishedGoods: false,
            finishedGoods: [],
            spareParts: [],
            selectedFinishedGoods: [],
            selectedSpareParts: [],
            finishedGoodHeaders: [
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
        }
    }
}
</script>
