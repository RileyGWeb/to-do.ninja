'use client';

import { useState, useEffect, useCallback, useRef, FormEvent } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { useAuth } from '@/lib/auth';
import { api } from '@/lib/api';
import { Project, TaskList, Task } from '@/lib/types';
import Header from '@/components/Header';
import InlineEdit from '@/components/InlineEdit';

export default function ProjectDetailPage() {
  const { user, loading } = useAuth();
  const router = useRouter();
  const params = useParams();
  const projectId = Number(params.id);

  const [project, setProject] = useState<Project | null>(null);
  const [selectedListId, setSelectedListId] = useState<number | null>(null);
  const [loadingProject, setLoadingProject] = useState(true);
  const [showTaskOptions, setShowTaskOptions] = useState(true);
  const [mobileShowLists, setMobileShowLists] = useState(false);
  const [newListName, setNewListName] = useState('');
  const [showNewListInput, setShowNewListInput] = useState(false);
  const [newTaskName, setNewTaskName] = useState('');
  const newListInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (!loading && !user) {
      router.replace('/login');
    }
  }, [user, loading, router]);

  const fetchProject = useCallback(async () => {
    try {
      const data = await api.getProject(projectId);
      setProject(data);
      if (data.task_lists && data.task_lists.length > 0 && !selectedListId) {
        const incompleteLists = data.task_lists.filter((l: TaskList) => !l.completed);
        if (incompleteLists.length > 0) {
          setSelectedListId(incompleteLists[0].id);
        } else {
          setSelectedListId(data.task_lists[0].id);
        }
      }
    } catch {
      router.replace('/projects');
    } finally {
      setLoadingProject(false);
    }
  }, [projectId, router, selectedListId]);

  useEffect(() => {
    if (user) fetchProject();
  }, [user, fetchProject]);

  const selectedList = project?.task_lists?.find((l) => l.id === selectedListId) || null;

  const handleRenameProject = async (name: string) => {
    await api.updateProject(projectId, { name });
    fetchProject();
  };

  const handleAddList = async () => {
    if (!newListName.trim()) return;
    const created = await api.createList(projectId, newListName.trim());
    setNewListName('');
    setShowNewListInput(false);
    await fetchProject();
    setSelectedListId(created.id);
  };

  const handleRenameList = async (listId: number, name: string) => {
    await api.updateList(listId, { name });
    fetchProject();
  };

  const handleDeleteList = async (listId: number) => {
    await api.deleteList(listId);
    if (selectedListId === listId) {
      setSelectedListId(null);
    }
    fetchProject();
  };

  const handleAddTask = async (e: FormEvent) => {
    e.preventDefault();
    if (!newTaskName.trim() || !selectedListId) return;
    await api.createTask(selectedListId, newTaskName.trim());
    setNewTaskName('');
    fetchProject();
  };

  const handleToggleTask = async (task: Task) => {
    await api.updateTask(task.id, { completed: !task.completed });
    fetchProject();
  };

  const handleDeleteTask = async (taskId: number) => {
    await api.deleteTask(taskId);
    fetchProject();
  };

  const handleRenameTask = async (taskId: number, name: string) => {
    await api.updateTask(taskId, { name });
    fetchProject();
  };

  const handleUpdateTaskNotes = async (taskId: number, notes: string) => {
    await api.updateTask(taskId, { notes });
    fetchProject();
  };

  const handleTaskReorder = async (listId: number, taskIds: number[]) => {
    await api.reorderTasks(listId, taskIds);
    fetchProject();
  };

  useEffect(() => {
    if (showNewListInput && newListInputRef.current) {
      newListInputRef.current.focus();
    }
  }, [showNewListInput]);

  if (loading || loadingProject || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-400" />
      </div>
    );
  }

  if (!project) return null;

  const incompleteLists = (project.task_lists || []).filter((l) => !l.completed);
  const completedLists = (project.task_lists || []).filter((l) => l.completed);

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-6xl mx-auto px-4 py-6">
        <div className="text-center mb-6">
          <InlineEdit
            value={project.name}
            onSave={handleRenameProject}
            className="text-3xl text-gray-700"
            tag="h1"
            inputClassName="text-3xl text-gray-700 text-center"
          />
        </div>

        <div className="flex items-center justify-end mb-2">
          <button
            onClick={() => setShowTaskOptions(!showTaskOptions)}
            className="text-xs text-gray-400 hover:text-gray-600 cursor-pointer"
          >
            {showTaskOptions ? 'hide task options' : 'show task options'}
          </button>
        </div>

        {/* Mobile: toggle button */}
        <div className="md:hidden mb-4">
          <button
            onClick={() => setMobileShowLists(!mobileShowLists)}
            className="flex items-center gap-2 text-sm text-gray-500 px-3 py-2 rounded-lg border border-gray-300 cursor-pointer"
          >
            <svg width="16" height="12" viewBox="0 0 16 12" fill="none" stroke="currentColor" strokeWidth="1.5">
              <line x1="0" y1="1" x2="16" y2="1" />
              <line x1="0" y1="6" x2="16" y2="6" />
              <line x1="0" y1="11" x2="16" y2="11" />
            </svg>
            LISTS
          </button>
        </div>

        <div className="flex gap-4">
          {/* Lists Sidebar */}
          <div className={`${mobileShowLists ? 'block' : 'hidden'} md:block w-full md:w-64 flex-shrink-0`}>
            <div className="bg-white rounded-xl border border-gray-200 p-4">
              <div className="flex items-center justify-between mb-3">
                <h3
                  className="text-sm tracking-wider text-gray-500"
                  style={{ fontFamily: "'Julius Sans One', sans-serif" }}
                >
                  LISTS
                </h3>
                <button
                  onClick={() => setShowNewListInput(true)}
                  className="w-6 h-6 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs hover:bg-gray-500 cursor-pointer"
                >
                  +
                </button>
              </div>

              {showNewListInput && (
                <form
                  onSubmit={(e) => { e.preventDefault(); handleAddList(); }}
                  className="mb-2"
                >
                  <div className="flex bg-gray-100 rounded-full h-8 items-center px-3">
                    <input
                      ref={newListInputRef}
                      type="text"
                      value={newListName}
                      onChange={(e) => setNewListName(e.target.value)}
                      onBlur={() => { if (!newListName.trim()) setShowNewListInput(false); }}
                      placeholder="Name your list..."
                      className="bg-transparent border-none outline-none text-sm flex-1 focus:ring-0 p-0"
                    />
                  </div>
                </form>
              )}

              <div className="space-y-1">
                {incompleteLists.map((list) => (
                  <ListSidebarItem
                    key={list.id}
                    list={list}
                    isSelected={selectedListId === list.id}
                    onClick={() => { setSelectedListId(list.id); setMobileShowLists(false); }}
                  />
                ))}
              </div>

              {completedLists.length > 0 && (
                <CompletedSection label="Completed">
                  <div className="space-y-1">
                    {completedLists.map((list) => (
                      <ListSidebarItem
                        key={list.id}
                        list={list}
                        isSelected={selectedListId === list.id}
                        completed
                        onClick={() => { setSelectedListId(list.id); setMobileShowLists(false); }}
                      />
                    ))}
                  </div>
                </CompletedSection>
              )}
            </div>
          </div>

          {/* Task Panel */}
          <div className="flex-1 min-w-0">
            {selectedList ? (
              <div className="bg-white rounded-xl border border-gray-200 p-6">
                <div className="text-center mb-4">
                  <InlineEdit
                    value={selectedList.name}
                    onSave={(name) => handleRenameList(selectedList.id, name)}
                    className="text-xl text-gray-600 tracking-wider"
                    tag="h2"
                    inputClassName="text-xl text-gray-600 text-center tracking-wider"
                  />

                  {showTaskOptions && (
                    <button
                      onClick={() => handleDeleteList(selectedList.id)}
                      className="text-xs text-red-400 hover:text-red-600 mt-1 cursor-pointer"
                    >
                      Delete list
                    </button>
                  )}
                </div>

                <form onSubmit={handleAddTask} className="flex gap-3 mb-4">
                  <input
                    type="text"
                    value={newTaskName}
                    onChange={(e) => setNewTaskName(e.target.value)}
                    placeholder="what are we working on?"
                    className="flex-1 px-4 py-2 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                  />
                  <button
                    type="submit"
                    className="px-6 py-2 rounded-full text-white font-semibold text-sm bg-gradient-to-r from-[#FFE000] to-[#FFC200] hover:opacity-90 transition-opacity whitespace-nowrap cursor-pointer"
                  >
                    Add task
                  </button>
                </form>

                <hr className="mx-8 my-3 border-gray-200" />

                <TaskListView
                  tasks={selectedList.tasks || []}
                  showOptions={showTaskOptions}
                  onToggle={handleToggleTask}
                  onDelete={handleDeleteTask}
                  onRename={handleRenameTask}
                  onUpdateNotes={handleUpdateTaskNotes}
                  onReorder={(taskIds) => handleTaskReorder(selectedList.id, taskIds)}
                />
              </div>
            ) : (
              <div className="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-400">
                {(project.task_lists || []).length === 0
                  ? 'Create a list to get started'
                  : 'Select a list to view tasks'}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

function ListSidebarItem({ list, isSelected, completed, onClick }: {
  list: TaskList;
  isSelected: boolean;
  completed?: boolean;
  onClick: () => void;
}) {
  return (
    <button
      onClick={onClick}
      className={`w-full flex items-center px-3 h-8 rounded-full text-sm transition-all cursor-pointer ${
        isSelected ? 'bg-gray-200 border-2 border-gray-400 shadow-sm' : 'bg-gray-100 hover:bg-gray-200'
      } ${completed ? 'text-gray-400 line-through' : 'text-gray-600'}`}
    >
      <span className="truncate max-w-[10em]">{list.name}</span>
      <span className="opacity-50 ml-1 text-xs whitespace-nowrap">
        - {list.completed_tasks}/{list.total_tasks}
      </span>
      <span className="ml-auto flex-shrink-0">
        {completed ? (
          <span className="h-5 w-5 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
            <svg width="10" height="8" viewBox="0 0 14 10" fill="none">
              <path d="M1 5L5 9L13 1" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </span>
        ) : (
          <svg width="8" height="12" viewBox="0 0 8 12" fill="none" className="text-gray-400">
            <path d="M1 1L6 6L1 11" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
          </svg>
        )}
      </span>
    </button>
  );
}

function CompletedSection({ label, children }: { label: string; children: React.ReactNode }) {
  const [open, setOpen] = useState(true);

  return (
    <div className="mt-4">
      <button
        onClick={() => setOpen(!open)}
        className="flex items-center w-full mb-2 py-1 px-2 rounded-lg transition cursor-pointer hover:bg-gray-100"
      >
        <hr className="w-6 border-gray-400" />
        <span className="mx-2 text-gray-500 text-xs whitespace-nowrap">{label}</span>
        <hr className="flex-1 border-gray-400" />
        <svg
          className={`ml-1 w-2 h-2 text-gray-400 transition-transform ${open ? 'rotate-90' : '-rotate-90'}`}
          viewBox="0 0 8 12"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M1 1L6 6L1 11" />
        </svg>
      </button>
      {open && children}
    </div>
  );
}

function TaskListView({ tasks, showOptions, onToggle, onDelete, onRename, onUpdateNotes, onReorder }: {
  tasks: Task[];
  showOptions: boolean;
  onToggle: (task: Task) => void;
  onDelete: (taskId: number) => void;
  onRename: (taskId: number, name: string) => void;
  onUpdateNotes: (taskId: number, notes: string) => void;
  onReorder: (taskIds: number[]) => void;
}) {
  const incompleteTasks = tasks.filter((t) => !t.completed);
  const completedTasks = tasks.filter((t) => t.completed);

  return (
    <div>
      <DraggableTaskList
        tasks={incompleteTasks}
        showOptions={showOptions}
        onToggle={onToggle}
        onDelete={onDelete}
        onRename={onRename}
        onUpdateNotes={onUpdateNotes}
        onReorder={onReorder}
        allTasks={tasks}
      />

      {completedTasks.length > 0 && (
        <CompletedSection label="Completed">
          <div className="space-y-2">
            {completedTasks.map((task) => (
              <TaskItem
                key={task.id}
                task={task}
                showOptions={showOptions}
                onToggle={() => onToggle(task)}
                onDelete={() => onDelete(task.id)}
                onRename={(name) => onRename(task.id, name)}
                onUpdateNotes={(notes) => onUpdateNotes(task.id, notes)}
              />
            ))}
          </div>
        </CompletedSection>
      )}
    </div>
  );
}

function DraggableTaskList({ tasks, showOptions, onToggle, onDelete, onRename, onUpdateNotes, onReorder, allTasks }: {
  tasks: Task[];
  showOptions: boolean;
  onToggle: (task: Task) => void;
  onDelete: (taskId: number) => void;
  onRename: (taskId: number, name: string) => void;
  onUpdateNotes: (taskId: number, notes: string) => void;
  onReorder: (taskIds: number[]) => void;
  allTasks: Task[];
}) {
  const [draggedId, setDraggedId] = useState<number | null>(null);
  const [dragOverId, setDragOverId] = useState<number | null>(null);
  const draggedRef = useRef<number | null>(null);

  const handleDragStart = (taskId: number) => {
    draggedRef.current = taskId;
    setDraggedId(taskId);
  };

  const handleDragOver = (e: React.DragEvent, taskId: number) => {
    e.preventDefault();
    if (draggedRef.current !== null && draggedRef.current !== taskId) {
      setDragOverId(taskId);
    }
  };

  const handleDrop = (e: React.DragEvent, dropTargetId: number) => {
    e.preventDefault();
    if (draggedRef.current === null || draggedRef.current === dropTargetId) return;

    const currentOrder = tasks.map((t) => t.id);
    const dragIdx = currentOrder.indexOf(draggedRef.current);
    const dropIdx = currentOrder.indexOf(dropTargetId);

    if (dragIdx === -1 || dropIdx === -1) return;

    currentOrder.splice(dragIdx, 1);
    currentOrder.splice(dropIdx, 0, draggedRef.current);

    const completedIds = allTasks.filter((t) => t.completed).map((t) => t.id);
    onReorder([...currentOrder, ...completedIds]);

    setDraggedId(null);
    setDragOverId(null);
    draggedRef.current = null;
  };

  const handleDragEnd = () => {
    setDraggedId(null);
    setDragOverId(null);
    draggedRef.current = null;
  };

  return (
    <div className="space-y-2">
      {tasks.map((task) => (
        <div
          key={task.id}
          draggable
          onDragStart={() => handleDragStart(task.id)}
          onDragOver={(e) => handleDragOver(e, task.id)}
          onDrop={(e) => handleDrop(e, task.id)}
          onDragEnd={handleDragEnd}
          className={`transition-all ${
            draggedId === task.id ? 'opacity-50' : ''
          } ${dragOverId === task.id ? 'border-t-2 border-yellow-400' : ''}`}
        >
          <TaskItem
            task={task}
            showOptions={showOptions}
            onToggle={() => onToggle(task)}
            onDelete={() => onDelete(task.id)}
            onRename={(name) => onRename(task.id, name)}
            onUpdateNotes={(notes) => onUpdateNotes(task.id, notes)}
          />
        </div>
      ))}
    </div>
  );
}

function TaskItem({ task, showOptions, onToggle, onDelete, onRename, onUpdateNotes }: {
  task: Task;
  showOptions: boolean;
  onToggle: () => void;
  onDelete: () => void;
  onRename: (name: string) => void;
  onUpdateNotes: (notes: string) => void;
}) {
  const [expanded, setExpanded] = useState(false);
  const [notesValue, setNotesValue] = useState(task.notes || '');

  useEffect(() => {
    setNotesValue(task.notes || '');
  }, [task.notes]);

  const handleNotesBlur = () => {
    if (notesValue !== (task.notes || '')) {
      onUpdateNotes(notesValue);
    }
  };

  return (
    <div>
      <div className={`flex items-center bg-white rounded-full border border-gray-200 px-3 py-2 gap-2 ${task.completed ? 'opacity-70' : ''}`}>
        <button
          onClick={onToggle}
          className="flex-shrink-0 cursor-pointer group"
        >
          {task.completed ? (
            <span className="h-6 w-6 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
              <svg width="12" height="9" viewBox="0 0 14 10" fill="none">
                <path d="M1 5L5 9L13 1" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
            </span>
          ) : (
            <span className="h-6 w-6 border border-gray-300 rounded-full flex items-center justify-center group-hover:bg-gradient-to-r group-hover:from-[#FFE000] group-hover:to-[#FFC200] group-hover:opacity-50">
              <svg width="12" height="9" viewBox="0 0 14 10" fill="none" className="hidden group-hover:block">
                <path d="M1 5L5 9L13 1" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
            </span>
          )}
        </button>

        <div className={`flex-1 min-w-0 ${task.completed ? 'line-through text-gray-400' : 'text-gray-700'}`}>
          <InlineEdit
            value={task.name}
            onSave={onRename}
            className="text-sm"
            inputClassName="text-sm"
          />
        </div>

        {showOptions && (
          <div className="flex items-center gap-2 flex-shrink-0">
            <button
              onClick={onDelete}
              className="text-gray-300 hover:text-gray-500 cursor-pointer"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <polyline points="3,6 5,6 21,6" />
                <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6" />
              </svg>
            </button>
            <button
              onClick={() => setExpanded(!expanded)}
              className={`text-gray-300 hover:text-gray-500 cursor-pointer transition-transform ${expanded ? 'rotate-180' : ''}`}
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <polyline points="6,9 12,15 18,9" />
              </svg>
            </button>
          </div>
        )}
      </div>

      {expanded && showOptions && (
        <div className="ml-10 mr-4 mt-1 mb-2">
          <textarea
            value={notesValue}
            onChange={(e) => setNotesValue(e.target.value)}
            onBlur={handleNotesBlur}
            placeholder="Add notes..."
            className="w-full text-sm text-gray-500 border border-gray-200 rounded-lg p-2 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 resize-none"
            rows={3}
          />
        </div>
      )}
    </div>
  );
}
