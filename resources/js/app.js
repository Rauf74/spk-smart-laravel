import { initAuthLogin } from './modules/auth-login';
import { initDataTables } from './modules/datatable-init';
import { initConfirmDelete } from './modules/confirm-delete';
import { initCommonUI } from './modules/common-ui';

document.addEventListener('DOMContentLoaded', () => {
    initAuthLogin();
    initDataTables();
    initConfirmDelete();
    initCommonUI();
});
