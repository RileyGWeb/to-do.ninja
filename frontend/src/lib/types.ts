export interface User {
  id: number;
  name: string;
  email: string;
}

export interface Project {
  id: number;
  user_id: number;
  name: string;
  completed: boolean;
  sort_order: number;
  total_lists: number;
  completed_lists: number;
  total_tasks: number;
  completed_tasks: number;
  task_lists?: TaskList[];
}

export interface TaskList {
  id: number;
  project_id: number;
  user_id: number;
  name: string;
  completed: boolean;
  sort_order: number;
  total_tasks: number;
  completed_tasks: number;
  tasks?: Task[];
}

export interface Task {
  id: number;
  task_list_id: number;
  project_id: number;
  user_id: number;
  name: string;
  completed: boolean;
  sort_order: number;
  notes: string | null;
}
