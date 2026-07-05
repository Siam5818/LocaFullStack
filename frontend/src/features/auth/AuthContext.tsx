/* cspell:disable */

import {
  createContext,
  useContext,
  useState,
  useEffect,
  type PropsWithChildren,
} from "react";
import type { Personne, LoginPayload } from "@/entities/personne/types";
import { login as loginApi, logout as logoutApi, getMe } from "./api";

interface AuthContextValue {
  user: Personne | null;
  isLoading: boolean;
  login: (payload: LoginPayload) => Promise<Personne["role"]>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export function AuthProvider({ children }: Readonly<PropsWithChildren>) {
  const [user, setUser] = useState<Personne | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("auth_token");
    if (!token) {
      setIsLoading(false);
      return;
    }
    getMe()
      .then(setUser)
      .catch(() => localStorage.removeItem("auth_token"))
      .finally(() => setIsLoading(false));
  }, []);

  async function login(payload: LoginPayload) {
    const data = await loginApi(payload);
    localStorage.setItem("auth_token", data.token);
    const me = await getMe();
    setUser(me);
    return me.role;
  }

  async function logout() {
    try {
      await logoutApi();
    } finally {
      localStorage.removeItem("auth_token");
      setUser(null);
    }
  }

  return (
    <AuthContext.Provider value={{ user, isLoading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuthContext() {
  const ctx = useContext(AuthContext);
  if (!ctx)
    throw new Error("useAuthContext doit être utilisé dans AuthProvider");
  return ctx;
}
