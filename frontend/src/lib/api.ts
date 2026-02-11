const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

function getToken(): string | null {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem('token');
}

async function request(endpoint: string, options: RequestInit = {}) {
  const token = getToken();
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...(options.headers as Record<string, string> || {}),
  };
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const res = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers,
  });

  if (res.status === 401) {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    throw new Error('Unauthorized');
  }

  if (!res.ok) {
    const data = await res.json().catch(() => ({}));
    throw new Error(data.message || `Request failed: ${res.status}`);
  }

  return res.json();
}

export const api = {
  register: (data: { name: string; email: string; password: string; password_confirmation: string }) =>
    request('/register', { method: 'POST', body: JSON.stringify(data) }),

  login: (data: { email: string; password: string }) =>
    request('/login', { method: 'POST', body: JSON.stringify(data) }),

  logout: () => request('/logout', { method: 'POST' }),

  getUser: () => request('/user'),

  getProjects: () => request('/projects'),

  createProject: (name: string) =>
    request('/projects', { method: 'POST', body: JSON.stringify({ name }) }),

  getProject: (id: number) => request(`/projects/${id}`),

  updateProject: (id: number, data: { name?: string }) =>
    request(`/projects/${id}`, { method: 'PUT', body: JSON.stringify(data) }),

  deleteProject: (id: number) =>
    request(`/projects/${id}`, { method: 'DELETE' }),

  reorderProjects: (order: number[]) =>
    request('/projects/reorder', { method: 'POST', body: JSON.stringify({ order }) }),

  createList: (projectId: number, name: string) =>
    request(`/projects/${projectId}/lists`, { method: 'POST', body: JSON.stringify({ name }) }),

  updateList: (id: number, data: { name?: string }) =>
    request(`/lists/${id}`, { method: 'PUT', body: JSON.stringify(data) }),

  deleteList: (id: number) =>
    request(`/lists/${id}`, { method: 'DELETE' }),

  reorderLists: (projectId: number, order: number[]) =>
    request(`/projects/${projectId}/lists/reorder`, { method: 'POST', body: JSON.stringify({ order }) }),

  createTask: (listId: number, name: string) =>
    request(`/lists/${listId}/tasks`, { method: 'POST', body: JSON.stringify({ name }) }),

  updateTask: (id: number, data: { name?: string; completed?: boolean; notes?: string }) =>
    request(`/tasks/${id}`, { method: 'PUT', body: JSON.stringify(data) }),

  deleteTask: (id: number) =>
    request(`/tasks/${id}`, { method: 'DELETE' }),

  reorderTasks: (listId: number, order: number[]) =>
    request(`/lists/${listId}/tasks/reorder`, { method: 'POST', body: JSON.stringify({ order }) }),
};
