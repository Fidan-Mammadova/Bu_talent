export interface Todo {
  id: number;
  content: string;
}

export interface Meta {
  totalCount: number;
}

export interface User {
  id?: string;
  username: string;
  email: string;
  password: string;
  firstName?: string;
  lastName?: string;
  phone?: string;
  avatar?: string;
  createdAt?: Date;
  updatedAt?: Date;
}

export interface LoginForm {
  emailOrPhone: string;
  password: string;
  rememberMe?: boolean;
}

export interface SignUpForm {
  username: string;
  emailOrPhone: string;
  password: string;
  firstName?: string;
  lastName?: string;
  agreeToTerms: boolean;
  agreeToPrivacy: boolean;
}

export interface ValidationError {
  field: string;
  message: string;
}

export interface AuthResponse {
  user: User;
  token: string;
  refreshToken?: string;
}

// Валидация email
export const validateEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

// Валидация пароля
export const validatePassword = (password: string): { isValid: boolean; errors: string[] } => {
  const errors: string[] = [];
  
  if (password.length < 8) {
    errors.push('Пароль должен содержать минимум 8 символов');
  }
  
  if (!/[A-Z]/.test(password)) {
    errors.push('Пароль должен содержать хотя бы одну заглавную букву');
  }
  
  if (!/[a-z]/.test(password)) {
    errors.push('Пароль должен содержать хотя бы одну строчную букву');
  }
  
  if (!/\d/.test(password)) {
    errors.push('Пароль должен содержать хотя бы одну цифру');
  }
  
  if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
    errors.push('Пароль должен содержать хотя бы один специальный символ');
  }
  
  return {
    isValid: errors.length === 0,
    errors
  };
};

// Валидация имени пользователя
export const validateUsername = (username: string): { isValid: boolean; errors: string[] } => {
  const errors: string[] = [];
  
  if (username.length < 3) {
    errors.push('Username must contain at least 3 characters');
  }
  
  if (username.length > 20) {
    errors.push('Username must not exceed 20 characters');
  }
  
  if (!/^[a-zA-Z0-9_]+$/.test(username)) {
    errors.push('Username can only contain letters, numbers, and underscores');
  }
  
  return {
    isValid: errors.length === 0,
    errors
  };
};
