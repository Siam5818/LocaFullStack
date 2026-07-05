/* cspell:disable */

import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { QueryProvider } from "./app/providers/QueryProvider";
import { AppRouter } from "./app/router";
import "./index.css";
import { AuthProvider } from "./app/providers/AuthProvider";

createRoot(document.getElementById("root")!).render(
  <StrictMode>
    <QueryProvider>
      <AuthProvider>
        <AppRouter />
      </AuthProvider>
    </QueryProvider>
  </StrictMode>,
);
