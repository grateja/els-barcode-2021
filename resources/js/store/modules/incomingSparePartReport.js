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
        return axios.post('/api/incoming-reports/spare-parts/create', data.formData).then((res, rej) => {
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
        return axios.post(`/api/incoming-reports/spare-parts/${data.reportId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteReport(context, reportId) {
        return axios.post(`/api/incoming-reports/spare-parts/${reportId}/delete`);
    },
    addItems(context, data) {
        return axios.post(`/api/incoming-reports/spare-parts/${data.reportId}/add-items`, data.formData);
    },
    removeItems(context, data) {
        return axios.post(`/api/incoming-reports/spare-parts/${data.reportId}/remove-items`, data.formData);
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
