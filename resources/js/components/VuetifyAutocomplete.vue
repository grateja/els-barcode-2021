<template>
    <div class="autocomplete">
        <v-combobox
            outline
            :loading="loading"
            :error-messages="errorMessages"
            :label="label"
            :value="value"
            :items="items"
            @input.native="input($event)"
            @change="select"
            @keydown.native.enter="$emit('enter', $event)">
        </v-combobox>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
        url: '',
        dataSource: {
            default: 'data'
        },
        dataField: {
            default: 'display'
        },
        label: {},
        errorMessages: {}
    },
    data() {
        return {
            keyword: '',
            cancelSource: null,
            raw: [],
            loading: false
        }
    },
    methods: {
        select(e) {
            let selected = this.raw.filter(d => d[this.dataField] == e);
            console.log('select');
            if(selected.length) {
                this.$emit('select', selected[0]);
                this.$emit('input', e);
                this.$emit('change', e);
            }
            this.raw = [];
        },
        input(e) {
            let val = e.target.value;

            this.$emit('input', val);

            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            if(val.length > 0){
                this.loading = true;
                axios.get(this.url, {
                    params: {keyword: val},
                    cancelToken: this.cancelSource.token
                }).then((res) => {
                    this.raw = res.data[this.dataSource];
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                });
            } else {
                this.raw = [];
            }
        },
        cancelSearch(){
            if(this.cancelSource){
                this.raw = [];
                this.cancelSource.cancel();
            }
        }
    },
    computed: {
        items() {
            return this.raw.map(item => item[this.dataField]);
        }
    }
}
</script>

