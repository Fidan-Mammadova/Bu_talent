import { ref, computed, readonly } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/authService';
import type { User, LoginForm, SignUpForm } from '../components/models';

export function useAuth() {
  const router = useRouter();
  
  // Reactive state
  const user = ref<User | null>(null);
  const isAuthenticated = ref(false);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  // Initialize auth state
  const initializeAuth = () => {
    if (authService.isAuthenticated()) {
      const currentUser = authService.getCurrentUser();
      if (currentUser) {
        user.value = currentUser;
        isAuthenticated.value = true;
      }
    }
  };

  // Sign in
  const signIn = async (credentials: LoginForm) => {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await authService.signIn(credentials);
      user.value = response.user;
      isAuthenticated.value = true;
      
      return response;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error during sign in';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  // Sign up
  const signUp = async (userData: SignUpForm) => {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await authService.signUp(userData);
      user.value = response.user;
      isAuthenticated.value = true;
      
      return response;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error during registration';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  // Sign out
  const signOut = async () => {
    isLoading.value = true;
    error.value = null;
    
    try {
      await authService.signOut();
      user.value = null;
      isAuthenticated.value = false;
      await router.push('/auth');

    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error during sign out';
      console.error('Sign out error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  // Update profile
  const updateProfile = async (updates: Partial<User>) => {
    isLoading.value = true;
    error.value = null;
    
    try {
      const updatedUser = await authService.updateProfile(updates);
      user.value = updatedUser;
      
      return updatedUser;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error updating profile';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  // Change password
  const changePassword = async (currentPassword: string, newPassword: string) => {
    isLoading.value = true;
    error.value = null;
    
    try {
      await authService.changePassword(currentPassword, newPassword);
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error changing password';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  // Forgot password
  const forgotPassword = async (email: string) => {
    isLoading.value = true;
    error.value = null;
    
    try {
      await authService.forgotPassword(email);
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error during password recovery';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  // Reset password
  const resetPassword = async (token: string, newPassword: string) => {
    isLoading.value = true;
    error.value = null;
    
    try {
      await authService.resetPassword(token, newPassword);
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Error during password reset';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  // Refresh token
  const refreshToken = async () => {
    try {
      const success = await authService.refreshToken();
      if (success) {
        const currentUser = authService.getCurrentUser();
        if (currentUser) {
          user.value = currentUser;
          isAuthenticated.value = true;
        }
      } else {
        // Token refresh failed, sign out user
        user.value = null;
        isAuthenticated.value = false;
        await router.push('/auth');
      }
      return success;
    } catch (err) {
      console.error('Token refresh error:', err);
      user.value = null;
      isAuthenticated.value = false;
      await router.push('/auth');
      return false;
    }
  };

  // Clear error
  const clearError = () => {
    error.value = null;
  };

  // Computed properties
  const userDisplayName = computed(() => {
    if (!user.value) return '';
    
    if (user.value.firstName && user.value.lastName) {
      return `${user.value.firstName} ${user.value.lastName}`;
    }
    
    if (user.value.firstName) {
      return user.value.firstName;
    }
    
    return user.value.username;
  });

  const userInitials = computed(() => {
    if (!user.value) return '';
    
    if (user.value.firstName && user.value.lastName) {
      return `${user.value.firstName[0]}${user.value.lastName[0]}`.toUpperCase();
    }
    
    if (user.value.firstName) {
      return user.value.firstName[0]?.toUpperCase() || '';
    }
    
    return user.value.username[0]?.toUpperCase() || '';
  });

  // Initialize auth state when composable is created
  initializeAuth();

  return {
    // State (readonly to prevent external mutations)
    user: readonly(user),
    isAuthenticated: readonly(isAuthenticated),
    isLoading: readonly(isLoading),
    error: readonly(error),
    
    // Computed properties
    userDisplayName,
    userInitials,
    
    // Methods
    signIn,
    signUp,
    signOut,
    updateProfile,
    changePassword,
    forgotPassword,
    resetPassword,
    refreshToken,
    clearError,
    
    // Utility methods
    initializeAuth,
  };
}
