import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    isLoading: false,
    isUpdatingEmail: false
};

const mutations = {
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setUpdatingStatus(state, status) {
        state.isUpdating = status;
    },
    setLoadingState(state, status) {
        state.isLoading = status;
    },
    setUpdatingEmailStatus(state, status) {
        state.isUpdatingEmail = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    insertProfile(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/users/self/create`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateProfile(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/account/${data.id}/update-profile`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    getAccountInfo(context, id) {
        context.commit('setLoadingState', true);
        return axios.get(`/api/account/${id}/get-account-info`).then((res, rej) => {
            context.commit('setLoadingState', false);
            return res;
        }).catch(err => {
            context.commit('setLoadingState', false);
            return Promise.reject(err);
        });
    },
    updateEmail(context, data) {
        context.commit('setUpdatingEmailStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/account/${data.id}/update-email`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setUpdatingEmailStatus', false);
            context.commit('updateEmail', res.data.user, {root: true});
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setUpdatingEmailStatus', false);
            return Promise.reject(err);
        });
    },
    changePassword(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/account/${data.id}/update-password`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
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
    isLoading(state) {
        return state.isLoading;
    },
    isUpdatingEmail(state) {
        return state.isUpdatingEmail;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
