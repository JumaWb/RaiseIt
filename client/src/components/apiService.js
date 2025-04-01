// apiService.js
const BASEURL = "http://localhost/RaiseIt/api";

export const API_ENDPOINTS = {
  LOGIN: `${BASEURL}/login.php`,
  REGISTER: `${BASEURL}/register.php`,
    FORGOT_PASSWORD: `${BASEURL}/forgot_password.php`,
    RESET_PASSWORD: `${BASEURL}/reset_password.php`,
    GET_USER: `${BASEURL}/get_user.php`,
    UPDATE_USER: `${BASEURL}/update_user.php`,
    EVENTS : `${BASEURL}/events.php`,
    CREATE_EVENT : `${BASEURL}/create_event.php`,
    UPDATE_EVENT : `${BASEURL}/update_event.php`,
    DELETE_EVENT : `${BASEURL}/delete_event.php`,
};

export default API_ENDPOINTS;