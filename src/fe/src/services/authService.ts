import type { LoginForm, SignUpForm, AuthResponse, User } from '../components/models';

// API base URL - замените на ваш реальный API endpoint
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:3000/api';

// Local storage keys
const TOKEN_KEY = 'auth_token';
const USER_KEY = 'auth_user';
const REFRESH_TOKEN_KEY = 'refresh_token';

export class AuthService {
  private static instance: AuthService;
  private token: string | null = null;
  private user: User | null = null;

  private constructor() {
    this.loadFromStorage();
  }

  public static getInstance(): AuthService {
    if (!AuthService.instance) {
      AuthService.instance = new AuthService();
    }
    return AuthService.instance;
  }

  // Загрузка данных из localStorage
  private loadFromStorage(): void {
    this.token = localStorage.getItem(TOKEN_KEY);
    const userStr = localStorage.getItem(USER_KEY);
    if (userStr) {
      try {
        this.user = JSON.parse(userStr);
      } catch (error) {
        console.error('Error parsing user from storage:', error);
        this.clearStorage();
      }
    }
  }

  // Сохранение данных в localStorage
  private saveToStorage(token: string, user: User, refreshToken?: string): void {
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
    if (refreshToken) {
      localStorage.setItem(REFRESH_TOKEN_KEY, refreshToken);
    }
    
    this.token = token;
    this.user = user;
  }

  // Очистка localStorage
  private clearStorage(): void {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    localStorage.removeItem(REFRESH_TOKEN_KEY);
    
    this.token = null;
    this.user = null;
  }

  // Получение заголовков для API запросов
  private getHeaders(): HeadersInit {
    const headers: HeadersInit = {
      'Content-Type': 'application/json',
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    return headers;
  }

  // Регистрация пользователя
  async signUp(formData: SignUpForm): Promise<AuthResponse> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/signup`, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          username: formData.username,
          emailOrPhone: formData.emailOrPhone,
          password: formData.password,
          firstName: formData.firstName,
          lastName: formData.lastName
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Error during registration');
      }

      const data: AuthResponse = await response.json();
      
      // Сохраняем данные пользователя
      this.saveToStorage(data.token, data.user, data.refreshToken);
      
      return data;
    } catch (error) {
      console.error('Sign up error:', error);
      throw error;
    }
  }

  // Вход пользователя
  async signIn(formData: LoginForm): Promise<AuthResponse> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/signin`, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          emailOrPhone: formData.emailOrPhone,
          password: formData.password,
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Invalid email or password');
      }

      const data: AuthResponse = await response.json();
      
      // Сохраняем данные пользователя
      this.saveToStorage(data.token, data.user, data.refreshToken);
      
      return data;
    } catch (error) {
      console.error('Sign in error:', error);
      throw error;
    }
  }

  // Выход пользователя
  async signOut(): Promise<void> {
    try {
      if (this.token) {
        // Отправляем запрос на сервер для выхода (если нужно)
        await fetch(`${API_BASE_URL}/auth/signout`, {
          method: 'POST',
          headers: this.getHeaders(),
        });
      }
    } catch (error) {
      console.error('Sign out error:', error);
    } finally {
      // Очищаем локальные данные в любом случае
      this.clearStorage();
    }
  }

  // Обновление токена
  async refreshToken(): Promise<boolean> {
    try {
      const refreshToken = localStorage.getItem(REFRESH_TOKEN_KEY);
      if (!refreshToken) {
        return false;
      }

      const response = await fetch(`${API_BASE_URL}/auth/refresh`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ refreshToken }),
      });

      if (!response.ok) {
        throw new Error('Failed to refresh token');
      }

      const data: AuthResponse = await response.json();
      this.saveToStorage(data.token, data.user, data.refreshToken);
      return true;
    } catch (error) {
      console.error('Token refresh error:', error);
      this.clearStorage();
      return false;
    }
  }

  // Проверка аутентификации
  isAuthenticated(): boolean {
    return !!this.token && !!this.user;
  }

  // Получение текущего пользователя
  getCurrentUser(): User | null {
    return this.user;
  }

  // Получение токена
  getToken(): string | null {
    return this.token;
  }

  // Обновление профиля пользователя
  async updateProfile(updates: Partial<User>): Promise<User> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/profile`, {
        method: 'PUT',
        headers: this.getHeaders(),
        body: JSON.stringify(updates),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Error updating profile');
      }

      const updatedUser: User = await response.json();
      
      // Обновляем локальные данные
      if (this.user) {
        this.user = { ...this.user, ...updatedUser };
        localStorage.setItem(USER_KEY, JSON.stringify(this.user));
      }
      
      return updatedUser;
    } catch (error) {
      console.error('Profile update error:', error);
      throw error;
    }
  }

  // Изменение пароля
  async changePassword(currentPassword: string, newPassword: string): Promise<void> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/change-password`, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          currentPassword,
          newPassword,
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Error changing password');
      }
    } catch (error) {
      console.error('Password change error:', error);
      throw error;
    }
  }

  // Восстановление пароля
  async forgotPassword(email: string): Promise<void> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/forgot-password`, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({ email }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Error during password recovery');
      }
    } catch (error) {
      console.error('Forgot password error:', error);
      throw error;
    }
  }

  // Сброс пароля
  async resetPassword(token: string, newPassword: string): Promise<void> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/reset-password`, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          token,
          newPassword,
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Error during password reset');
      }
    } catch (error) {
      console.error('Reset password error:', error);
      throw error;
    }
  }
}

// Экспортируем экземпляр сервиса
export const authService = AuthService.getInstance();

// Экспортируем типы для использования в других компонентах
// export type { AuthService };
