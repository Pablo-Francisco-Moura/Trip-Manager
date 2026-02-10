import { reactive } from "vue";

const messages = {
  pt: {
    logout: "Sair",
    requests: "Requisições",
    create_new_request: "Criar nova requisição",
    create_request: "Criar requisição",
    requester: "Solicitante",
    destination: "Destino",
    departure: "Data de ida",
    return: "Data de volta",
    create: "Criar",
    apply: "Aplicar",
    clear: "Limpar",
    view: "Ver",
    approve: "Aprovar",
    cancel: "Cancelar",
    confirmation: "Confirmação",
    confirm: "Confirmar",
    cancel_action: "Cancelar",
    login: "Entrar",
    email: "Email",
    password: "Senha",
    logging: "Entrando...",
    login_button: "Entrar",
    admin_info: "Você é administrador — pode alterar o status das requisições",
    user_info: "Você não é administrador — apenas pode visualizar seus pedidos",
    filters: "Filtros",
    all: "Todos",
    cannot_cancel_approved: "Não é possível cancelar um pedido aprovado",
    approved: "Aprovado",
    requested: "Solicitado",
    canceled: "Cancelado",
    created_at: "Criado em",
    status: "Status",
    actions: "Ações",
    request: "Requisição",
    close: "Fechar",
  },
  en: {
    logout: "Logout",
    requests: "Requests",
    create_new_request: "Create new request",
    create_request: "Create Request",
    requester: "Requester",
    destination: "Destination",
    departure: "Departure",
    return: "Return",
    create: "Create",
    apply: "Apply",
    clear: "Clear",
    view: "View",
    approve: "Approve",
    cancel: "Cancel",
    confirmation: "Confirmation",
    confirm: "Confirm",
    cancel_action: "Cancel",
    login: "Login",
    email: "Email",
    password: "Password",
    logging: "Logging...",
    login_button: "Login",
    admin_info: "You are an admin — can change request statuses",
    user_info: "You are not an admin — can only view your requests",
    filters: "Filters",
    all: "All",
    cannot_cancel_approved: "Cannot cancel an approved request",
    approved: "Approved",
    requested: "Requested",
    canceled: "Canceled",
    created_at: "Created at",
    status: "Status",
    actions: "Actions",
    request: "Request",
    close: "Close",
  },
};

export default {
  install(app) {
    // choose initial locale: saved preference -> browser language -> 'pt'
    let initial = "pt";
    try {
      const saved =
        typeof localStorage !== "undefined" && localStorage.getItem("locale");
      if (saved) initial = saved;
      else if (typeof navigator !== "undefined") {
        const nav = (navigator.language || navigator.userLanguage || "")
          .slice(0, 2)
          .toLowerCase();
        if (messages[nav]) initial = nav;
      }
    } catch (e) {
      initial = "pt";
    }
    const state = reactive({ locale: initial });
    function t(key) {
      return messages[state.locale]?.[key] ?? key;
    }
    function setLocale(l) {
      state.locale = l;
      localStorage.setItem("locale", l);
      // notify listeners
      window.dispatchEvent(new CustomEvent("pref:localeChange", { detail: l }));
    }
    app.config.globalProperties.$t = t;
    app.config.globalProperties.$setLocale = setLocale;
    app.config.globalProperties.$locale = state;
    app.provide("i18n", { state, t, setLocale });
  },
};
