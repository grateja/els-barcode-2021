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
    insertReport(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/reservations/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateReport(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/reservations/${data.reservationId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteReport(context, reservationId) {
        return axios.post(`/api/reservations/${reservationId}/delete`);
    },
    addItems(context, data) {
        return axios.post(`/api/reservations/${data.payload}/${data.reservationId}/add-items`, data.formData);
    },
    addFinishedGoods(context, data) {
        data.payload = 'finished-goods';
        return context.dispatch('addItems', data);
    },
    addSpareParts(context, data) {
        data.payload = 'spare-parts';
        return context.dispatch('addItems', data);
    },
    removeItems(context, data) {
        return axios.post(`/api/reservations/${data.payload}/${data.reservationId}/remove-items`, data.formData);
    },
    removeFinishedGoods(context, data) {
        data.payload = 'finished-goods';
        return context.dispatch('removeItems', data);
    },
    removeSpareParts(context, data) {
        data.payload = 'spare-parts';
        return context.dispatch('removeItems', data);
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
