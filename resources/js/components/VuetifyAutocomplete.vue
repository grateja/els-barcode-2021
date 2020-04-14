<template>
    <div class="autocomplete">
        <v-combobox :loading="loading" :error-messages="ErrorMessage" :label="label" :value="value" :items="items" @input.native="input($event)" @change="select" @keydown.native.enter="$emit('enter', $event)"></v-combobox>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
        url: '',
        data_source: {
            default: 'data'
        },
        data_field: {
            default: 'name'
        },
        label: {},
        ErrorMessage: {}
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
            let selected = this.raw.filter(d => d[this.data_field] == e);
            if(selected.length) {
                this.$emit('select', selected[0]);
                this.$emit('input', e);
                this.$emit('change', e);
            }
        },
        input(e) {
            let val = e.target.value;

            this.$emit('input', val);

            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            console.log(this.url)

            if(val.length > 0){
                this.loading = true;
                axios.get(this.url, {
                    params: {keyword: val},
                    cancelToken: this.cancelSource.token
                }).then((res) => {
                    console.log(e);
                    this.raw = res.data[this.data_source];
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                    // console.log(err);
                });
            } else {
                this.raw = [];
            }
        },
        cancelSearch(){
            if(this.cancelSource){
                this.cancelSource.cancel();
            }
        }
    },
    computed: {
        items() {
            return this.raw.map(item => item[this.data_field]);
        }
    }
}
</script>

