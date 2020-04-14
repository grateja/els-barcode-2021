<template>
    <v-card flat>
        <v-divider></v-divider>
        <v-card-actions>
            <v-btn class="mx-0" flat @click="$emit('input', parseInt(value) - 1)"><v-icon>chevron_left</v-icon>{{ parseInt(value) - 1}}</v-btn>
            <v-spacer></v-spacer>
            <span class="title grey--text">
                {{value}}
            </span>
            <v-spacer></v-spacer>
            <v-btn class="mx-0" flat @click="$emit('input', parseInt(value) + 1)">{{ parseInt(value) + 1}}<v-icon>chevron_right</v-icon></v-btn>
        </v-card-actions>
        <v-divider></v-divider>
    </v-card>
</template>

<script>
export default {
    props: [
        'value'
    ]
}
</script>
