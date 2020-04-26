import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false
};

const mutations = {
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    insertFinishedGood(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/finished-goods/items/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateFinishedGood(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/finished-goods/items/${data.serialNumber}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    insertSerial(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/finished-goods/items/${data.model}/insert-serial`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateSerial(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/finished-goods/items/${data.serialNumber}/update-serial`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteItem(context, serialNumber) {
        return axios.post(`/api/finished-goods/items/${serialNumber}/delete`);
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    isSaving(state) {
        return state.isSaving;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
