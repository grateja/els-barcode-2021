import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isLoading: false,
    isSaving: false,
    branches: [],
    roles: []
};

const mutations = {
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    setRoles(state, roles) {
        state.roles = roles;
    },
    setBranches(state, branches) {
        state.branches = branches;
    }
};

const actions = {
    insertUser(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/users/create`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateUser(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/users/${data.userId}/update`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteUser(context, userId) {
        return axios.post(`/api/users/${userId}/delete-user`);
    },
    changePassword(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/users/${data.userId}/change-password`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    assignRole(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/users/${data.userId}/assign-role`, data.formData).then((res, rej) => {
            console.log(res.data);
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    getRoles(context, data) {
        context.commit('setLoadingStatus', true);
        return axios.get('/api/all/roles').then((res, rej) => {
            context.commit('setLoadingStatus', false);
            context.commit('setRoles', res.data.roles);
            context.commit('setBranches', res.data.branches);
            return res;
        }).catch(err => {
            context.commit('setLoadingStatus', false);
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
    isLoading(state) {
        return state.isLoading;
    },
    getRoles(state) {
        return state.roles;
    },
    getBranches(state) {
        return state.branches;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
