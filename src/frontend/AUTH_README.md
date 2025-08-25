# Система аутентификации Bu Talent

## Обзор

Система аутентификации предоставляет полный функционал для регистрации, входа и управления профилем пользователей. Включает в себя:

- ✅ Форму входа с валидацией
- ✅ Форму регистрации с проверкой силы пароля
- ✅ Управление профилем пользователя
- ✅ Настройки аккаунта
- ✅ Социальные сети (готово к интеграции)
- ✅ Восстановление пароля
- ✅ Безопасное хранение токенов

## Структура файлов

```
src/
├── components/
│   ├── SignInForm.vue          # Форма входа
│   ├── SignUpForm.vue          # Форма регистрации
│   ├── UserProfile.vue         # Компонент профиля
│   └── models.ts               # Типы и интерфейсы
├── composables/
│   └── useAuth.ts              # Composable для управления аутентификацией
├── services/
│   └── authService.ts          # Сервис для API вызовов
├── pages/
│   ├── AuthPage.vue            # Страница аутентификации
│   └── ProfilePage.vue         # Страница профиля
└── router/
    └── routes.ts               # Маршруты приложения
```

## Основные компоненты

### 1. Форма входа (SignInForm.vue)

**Функции:**

- Валидация email и пароля
- Опция "Запомнить меня"
- Восстановление пароля
- Вход через социальные сети
- Обработка ошибок

**Использование:**

```vue
<SignInForm @success="onLoginSuccess" />
```

### 2. Форма регистрации (SignUpForm.vue)

**Функции:**

- Валидация всех полей
- Индикатор силы пароля
- Подтверждение пароля
- Согласие с условиями
- Проверка уникальности username

**Использование:**

```vue
<SignUpForm @success="onSignUpSuccess" />
```

### 3. Профиль пользователя (UserProfile.vue)

**Функции:**

- Отображение информации о пользователе
- Редактирование профиля
- Изменение пароля
- Статистика аккаунта
- Выход из системы

**Использование:**

```vue
<UserProfile />
```

## Composable useAuth

Основной composable для управления состоянием аутентификации:

```typescript
const {
  // Состояние
  user,
  isAuthenticated,
  isLoading,
  error,

  // Методы
  signIn,
  signUp,
  signOut,
  updateProfile,
  changePassword,

  // Вычисляемые свойства
  userDisplayName,
  userInitials,
} = useAuth();
```

## Сервис аутентификации

`AuthService` предоставляет методы для работы с API:

```typescript
import { authService } from "@/services/authService";

// Регистрация
await authService.signUp(userData);

// Вход
await authService.signIn(credentials);

// Выход
await authService.signOut();

// Обновление профиля
await authService.updateProfile(updates);
```

## Валидация

### Email

- Проверка формата email
- Обязательное поле

### Пароль

- Минимум 8 символов
- Заглавные и строчные буквы
- Цифры
- Специальные символы

### Имя пользователя

- 3-20 символов
- Только буквы, цифры и подчеркивание
- Уникальность (проверяется на сервере)

## Безопасность

- Токены хранятся в localStorage
- Автоматическое обновление токенов
- Валидация на клиенте и сервере
- Защита от XSS и CSRF атак

## Настройка

### 1. Переменные окружения

Создайте файл `.env` в корне frontend:

```env
VITE_API_URL=http://localhost:3000/api
VITE_APP_NAME=Bu Talent
VITE_ENABLE_SOCIAL_LOGIN=true
```

### 2. API Endpoints

Убедитесь, что ваш backend предоставляет следующие endpoints:

```
POST /api/auth/signup     # Регистрация
POST /api/auth/signin     # Вход
POST /api/auth/signout    # Выход
POST /api/auth/refresh    # Обновление токена
PUT  /api/auth/profile    # Обновление профиля
POST /api/auth/change-password    # Изменение пароля
POST /api/auth/forgot-password    # Восстановление пароля
POST /api/auth/reset-password     # Сброс пароля
```

### 3. Социальные сети

Для интеграции социальных сетей добавьте соответствующие API ключи:

```typescript
// В authService.ts
const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID;
const FACEBOOK_APP_ID = import.meta.env.VITE_FACEBOOK_APP_ID;
```

## Использование в компонентах

### Простой пример входа:

```vue
<template>
  <div>
    <SignInForm @success="handleLoginSuccess" />
  </div>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import SignInForm from "@/components/SignInForm.vue";

const router = useRouter();

const handleLoginSuccess = () => {
  router.push("/profile");
};
</script>
```

### Проверка аутентификации:

```vue
<template>
  <div>
    <div v-if="isAuthenticated">Добро пожаловать, {{ userDisplayName }}!</div>
    <div v-else>
      <router-link to="/auth">Войти</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from "@/composables/useAuth";

const { isAuthenticated, userDisplayName } = useAuth();
</script>
```

## Обработка ошибок

Система автоматически обрабатывает ошибки и показывает уведомления:

```typescript
// Ошибки валидации
if (!validateForm()) {
  return;
}

// API ошибки
try {
  await signIn(credentials);
} catch (error) {
  // Ошибка уже обработана в useAuth
  console.error("Login failed:", error);
}
```

## Тестирование

### Запуск в режиме разработки:

```bash
cd frontend
npm run dev
```

### Тестирование форм:

1. Откройте `/auth` для тестирования форм
2. Попробуйте различные сценарии валидации
3. Проверьте обработку ошибок
4. Протестируйте переходы между вкладками

## Расширение функционала

### Добавление новых полей в профиль:

1. Обновите интерфейс `User` в `models.ts`
2. Добавьте поля в форму редактирования
3. Обновите валидацию
4. Добавьте соответствующие API endpoints

### Интеграция новых социальных сетей:

1. Добавьте кнопку в формы
2. Реализуйте OAuth flow
3. Обновите `authService.ts`
4. Добавьте обработку токенов

## Поддержка

При возникновении проблем:

1. Проверьте консоль браузера на наличие ошибок
2. Убедитесь, что backend API работает корректно
3. Проверьте переменные окружения
4. Убедитесь, что все зависимости установлены

## Лицензия

MIT License
