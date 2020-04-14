<template>
    <v-snackbar v-model="openMessage" top @ended="ended" v-if="!!flashMessage" :color="flashMessage.color" multi-line>
        {{flashMessage.message}}
        <v-btn icon small @click="close">
            <v-icon small>close</v-icon>
        </v-btn>
    </v-snackbar>
</template>

<script>
export default {
    data() {
        return {
            openMessage: false
        }
    },
    methods: {
        ended() {
            console.log('ended');
        },
        close() {
            this.openMessage = false;
        }
    },
    computed: {
        flashMessage() {
            return this.$store.getters.getFlashMessage;
        }
    },
    watch: {
        flashMessage(val) {
            if(val) {
                this.openMessage = true;
            }
        }
    }
}
</script>
