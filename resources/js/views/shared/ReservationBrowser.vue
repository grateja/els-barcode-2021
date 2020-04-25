<template>
    <v-dialog :value="value" persistent max-width="620">
        <v-card>
            <v-card-title>
                <span class="grey--text">Search reservation</span>
                <v-spacer></v-spacer>
                <v-btn @click="close" icon small>
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-text-field label="Search client name" outline @keyup="load" v-model="keyword" ref="keyword"></v-text-field>
            </v-card-text>
            <v-card flat color="transparent" min-height="500" style="overflow-y:auto" max-height="600px">
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <tr :active="props.item.selected" :class="props.item.selected ? 'blue--text font-weight-bold' : ''" @click="preSelect(props.item)" class="cursor">
                            <td>{{props.index + 1}}</td>
                            <td>{{ moment(props.item.downpayment_date).format('YYYY, MMM DD') }}</td>
                            <td>{{ props.item.reference_number }}</td>
                            <td>{{ props.item.owner_name }}</td>
                            <td>{{ props.item.subdealer_name }}</td>
                        </tr>
                    </template>
                </v-data-table>
            </v-card>
            <v-card-actions>
                <v-btn round color="primary" @click="selectItem" v-if="!!selectedItem" :loading="addingItem">Continue ({{selectedItem.owner_name}})</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value'
    ],
    data() {
        return {
            loading: false,
            addingItem: false,
            openAddEdit: false,
            cancelSource: null,
            keyword: null,
            items: [],
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
                }
            ]
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            this.loading = true;
            axios.get(`/api/reservations`, {
                params: {
                    keyword: this.keyword
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.items = res.data.result.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        createNew() {
            this.openAddEdit = true;
        },
        preSelect(item) {
            this.items.forEach(i => {
                Vue.set(i, 'selected', item.id == i.id);
            });
        },
        selectItem() {
            if(!this.selectedItem) {
                alert("Please select one");
                return;
            }

            this._selectItem(this.selectedItem);
        },
        _selectItem(item) {
            this.$emit('select', item);
            this.close();
        }
    },
    computed: {
        selectedItem() {
            return this.items.find(i => i.selected);
        }
    },
    watch: {
        value(val) {
            if(val) {
                setTimeout(() => {
                    this.$refs.keyword.$el.querySelector('input').select();
                }, 500);
            }
            this.items = [];
        }
    }
}
</script>
