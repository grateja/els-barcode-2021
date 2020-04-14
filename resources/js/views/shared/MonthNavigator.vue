<template>
    <v-card flat>
        <v-divider></v-divider>
        <v-card-title class="ml-0 pa-0">
            <v-btn class="mx-0" flat v-if="monthIndex > 1" @click="prev"><v-icon>chevron_left</v-icon>{{months[monthIndex - 2]}}</v-btn>
            <v-spacer></v-spacer>
            <div class="title grey--text">{{months[monthIndex - 1]}}</div>
            <v-spacer></v-spacer>
            <v-btn class="mx-0" flat v-if="monthIndex < 12" @click="next">{{months[monthIndex]}}<v-icon>chevron_right</v-icon></v-btn>
        </v-card-title>
        <v-divider></v-divider>
    </v-card>
</template>

<script>
export default {
    props: [
        'value'
    ],
    data() {
        return {
            monthIndex: this.value || moment().format('M'),
            months: [
                'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
            ]
        }
    },
    methods: {
        prev() {
            this.monthIndex--;
            this.navigate();
        },
        next() {
            this.monthIndex++;
            this.navigate();
        },
        navigate() {
            this.$emit('input', this.monthIndex);
        }
    }
}
</script>
