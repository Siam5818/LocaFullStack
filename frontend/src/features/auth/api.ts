/* cspell:disable */

import apiClient from '@/shared/api/client';
import type { LoginPayload, RegisterPayload, LoginResponse, Personne } from '@/entities/personne/types';

export async function login(payload: LoginPayload) {
  const { data } = await apiClient.post<LoginResponse>('/login', payload);
  return data;
}

export async function register(payload: RegisterPayload) {
  const { data } = await apiClient.post<{ message: string }>('/register', payload);
  return data;
}

export async function logout() {
  await apiClient.post('/logout');
}

export async function getMe() {
  const { data } = await apiClient.get<Personne>('/me');
  return data;
}

export async function forgotPassword(email: string) {
  const { data } = await apiClient.post<{ message: string }>('/forgot-password', { email });
  return data;
}

export async function resetPassword(payload: {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
}) {
  const { data } = await apiClient.post<{ message: string }>('/reset-password', payload);
  return data;
}