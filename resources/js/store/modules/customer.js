import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    isLoading: false,
    currentCustomer: null
};

const mutations = {
    setCustomer(state, customer) {
        state.currentCustomer = customer;
    },
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setUpdatingStatus(state, status) {
        state.isUpdating = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    insertCustomer(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/customers/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            context.commit('setCustomer', res.data.customer);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateCustomer(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/customers/${data.customerId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteCustomer(context, customerId) {
        return axios.post(`/api/customers/${customerId}/delete-customer`);
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    isSaving(state) {
        return state.isSaving;
    },
    isUpdating(state) {
        return state.isUpdating;
    },
    getCustomer(state) {
        return state.currentCustomer;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
