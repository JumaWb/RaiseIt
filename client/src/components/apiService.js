const BASEURL = "http://localhost/RaiseIt/api";

export const API_ENDPOINTS = {
  // Authentication
  LOGIN: `${BASEURL}/auth/login`,
  REGISTER: `${BASEURL}/auth/register`,
  FORGOT_PASSWORD: `${BASEURL}/auth/forgot-password`,
  RESET_PASSWORD: `${BASEURL}/auth/reset-password`,
  
  // User Management
  GET_USER: `${BASEURL}/user/profile`,
  UPDATE_USER: `${BASEURL}/user/update`,
  
  // Events
  EVENTS: `${BASEURL}/events`,
  CREATE_EVENT: `${BASEURL}/events/create`,
  UPDATE_EVENT: `${BASEURL}/events/update`,
  DELETE_EVENT: `${BASEURL}/events/delete`,
  
  // Subscriptions
  SUBSCRIBE: `${BASEURL}/subscriptions`,
  GET_SUBSCRIPTION: `${BASEURL}/subscriptions/details`,
  CREATE_SUBSCRIPTION: `${BASEURL}/subscriptions/create`,
  UPDATE_SUBSCRIPTION: `${BASEURL}/subscriptions/update`,
  DELETE_SUBSCRIPTION: `${BASEURL}/subscriptions/cancel`,
  
  // Donations
  DONATIONS: `${BASEURL}/donations`,
  CREATE_DONATION: `${BASEURL}/donations/create`,
  UPDATE_DONATION: `${BASEURL}/donations/update`,
  DELETE_DONATION: `${BASEURL}/donations/cancel`,
  GET_DONATION: `${BASEURL}/donations/details`,
  
  // Payment Processing
  PAYMENTS: {
    // M-Pesa
    MPESA_STK_PUSH: `${BASEURL}/payments/mpesa/stk-push`,
    MPESA_CALLBACK: `${BASEURL}/payments/mpesa/callback`,
    MPESA_QUERY: `${BASEURL}/payments/mpesa/query`,
    
    // PayPal
    PAYPAL_CREATE_ORDER: `${BASEURL}/payments/paypal/create-order`,
    PAYPAL_CAPTURE_ORDER: `${BASEURL}/payments/paypal/capture-order`,
    PAYPAL_SUCCESS: `${BASEURL}/payments/paypal/success`,
    PAYPAL_CANCEL: `${BASEURL}/payments/paypal/cancel`,
    
    // Stripe
    STRIPE_CREATE_PAYMENT_INTENT: `${BASEURL}/payments/stripe/create-intent`,
    STRIPE_WEBHOOK: `${BASEURL}/payments/stripe/webhook`,
    STRIPE_CONFIRM_CARD: `${BASEURL}/payments/stripe/confirm-card`,
    
    // Flutterwave
    FLUTTERWAVE_INITIATE: `${BASEURL}/payments/flutterwave/initiate`,
    FLUTTERWAVE_CALLBACK: `${BASEURL}/payments/flutterwave/callback`,
    FLUTTERWAVE_VERIFY: `${BASEURL}/payments/flutterwave/verify`,
    
    // Pesapal
    PESAPAL_AUTH: `${BASEURL}/payments/pesapal/auth`,
    PESAPAL_ORDER: `${BASEURL}/payments/pesapal/create-order`,
    PESAPAL_CALLBACK: `${BASEURL}/payments/pesapal/callback`,
    PESAPAL_IPN: `${BASEURL}/payments/pesapal/ipn`,
    
    // General Payment
    PAYMENT_METHODS: `${BASEURL}/payments/methods`,
    PAYMENT_STATUS: `${BASEURL}/payments/status`,
    PAYMENT_RECEIPT: `${BASEURL}/payments/receipt`,
  },
  
  // Chat
  CHAT: `${BASEURL}/chat/messages`,
  START_CHAT: `${BASEURL}/chat/start`,
  
  // Reports
  DONATION_REPORT: `${BASEURL}/reports/donations`,
  TRANSACTION_REPORT: `${BASEURL}/reports/transactions`,
};