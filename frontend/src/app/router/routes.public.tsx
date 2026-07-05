/* cspell:disable */

import { Route } from 'react-router-dom';
import AccueilPage from '@/pages/public/AccueilPage';
import ProprietesPage from '@/pages/public/ProprietesPage';
import ProprieteDetailPage from '@/pages/public/ProprieteDetailPage';
import LoginPage from '@/pages/auth/LoginPage';
import RegisterPage from '@/pages/auth/RegisterPage';
import NotFoundPage from '@/pages/public/NotFoundPage';
import ForgotPasswordPage from '@/pages/auth/ForgotPasswordPage';
import ResetPasswordPage from '@/pages/auth/ResetPasswordPage';
import VerifyEmailPage from '@/pages/auth/VerifyEmailPage';
import ContactPage from '@/pages/public/ContactPage';

export const publicRoutes = (
  <>
    <Route path="/" element={<AccueilPage />} />
    <Route path="/proprietes" element={<ProprietesPage />} />
    <Route path="/proprietes/:id" element={<ProprieteDetailPage />} />
    <Route path="/contact" element={<ContactPage />} />
    <Route path="/connexion" element={<LoginPage />} />
    <Route path="/inscription" element={<RegisterPage />} />
    <Route path="/mot-de-passe-oublie" element={<ForgotPasswordPage />} />
    <Route path="/reinitialisation" element={<ResetPasswordPage />} />
    <Route path="/verification-email" element={<VerifyEmailPage />} />
    <Route path="*" element={<NotFoundPage />} />
  </>
);