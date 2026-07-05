/* cspell:disable */

import axios, { AxiosError } from 'axios';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: false,
  headers: { 
    Accept: 'application/json',
    'ngrok-skip-browser-warning': 'true' 
  },
});

apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

apiClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      if (globalThis.location.pathname !== '/connexion') {
        globalThis.location.href = '/connexion';
      }
    }
    return Promise.reject(error);
  }
);

export default apiClient;