// apiService.js
const BASEURL = "http://localhost/RaiseIt/api";

export const API_ENDPOINTS = {
  LOGIN: `${BASEURL}/login.php`,
  REGISTER: `${BASEURL}/register.php`,
    FORGOT_PASSWORD: `${BASEURL}/forgot_password.php`,
    RESET_PASSWORD: `${BASEURL}/reset_password.php`,
    GET_USER: `${BASEURL}/get_user.php`,
    UPDATE_USER: `${BASEURL}/update_user.php`,
};

export default API_ENDPOINTS;