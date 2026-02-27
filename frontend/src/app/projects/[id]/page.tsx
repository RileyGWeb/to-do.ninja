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
  const [deleteConfirmListId, setDeleteConfirmListId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [searchExpanded, setSearchExpanded] = useState(false);
  const newListInputRef = useRef<HTMLInputElement>(null);
  const searchInputRef = useRef<HTMLInputElement>(null);

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

  // Filter lists and tasks based on search query
  const filterListsAndTasks = () => {
    if (!searchQuery.trim()) {
      return {
        lists: project.task_lists || [],
        allMatch: true
      };
    }

    const query = searchQuery.toLowerCase();
    const filteredLists = (project.task_lists || []).map(list => {
      const listNameMatches = list.name.toLowerCase().includes(query);
      
      // Filter tasks within this list
      const filteredTasks = (list.tasks || []).filter(task => {
        const taskNameMatches = task.name.toLowerCase().includes(query);
        const notesMatch = (task.notes || '').toLowerCase().includes(query);
        return taskNameMatches || notesMatch;
      });

      // Include list if its name matches OR if it has matching tasks
      if (listNameMatches || filteredTasks.length > 0) {
        return {
          ...list,
          tasks: filteredTasks,
          _allTasksMatch: listNameMatches // If list name matches, show all tasks
        };
      }
      return null;
    }).filter(Boolean);

    return {
      lists: filteredLists,
      allMatch: false
    };
  };

  const { lists: filteredLists, allMatch } = filterListsAndTasks();

  const incompleteLists = (filteredLists as TaskList[]).filter((l) => !l.completed);
  const completedLists = (filteredLists as TaskList[]).filter((l) => l.completed);

  // Get selected list from filtered lists (or from original if showing all tasks in a matching list)
  let selectedList = (filteredLists as TaskList[]).find((l) => l.id === selectedListId) || null;
  
  // If we have a search query and the selected list exists in original but not filtered, 
  // it means the list doesn't match the search
  if (searchQuery && !selectedList && selectedListId) {
    const originalList = project?.task_lists?.find((l) => l.id === selectedListId);
    if (originalList) {
      // List exists but doesn't match search, so clear selection
      selectedList = null;
    }
  }

  return (
    <div className="h-screen bg-gray-50 flex flex-col overflow-hidden">
      <Header />
      <div className="flex-1 overflow-hidden flex flex-col">
        <div className="max-w-6xl mx-auto px-4 py-6 w-full flex flex-col h-full overflow-hidden">
        {/* Header with back button and title */}
        <div className="mb-4 flex-shrink-0">
          <button
            onClick={() => router.push('/projects')}
            className="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 cursor-pointer mb-3"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <polyline points="15,18 9,12 15,6" />
            </svg>
            <span className="hidden sm:inline">Back to Projects</span>
            <span className="sm:hidden">Back</span>
          </button>
          <div className="text-center">
            <InlineEdit
              value={project.name}
              onSave={handleRenameProject}
              className="text-2xl sm:text-3xl text-gray-700 break-words"
              tag="h1"
              inputClassName="text-2xl sm:text-3xl text-gray-700 text-center w-full"
            />
          </div>
        </div>

        <div className="flex items-center justify-end gap-4 mb-2 flex-shrink-0">
          {/* Search - Expandable */}
          <div className="flex items-center gap-2">
            {searchExpanded ? (
              <div className="relative flex items-center">
                <svg 
                  className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <circle cx="11" cy="11" r="8" strokeWidth="2" />
                  <path d="M21 21l-4.35-4.35" strokeWidth="2" strokeLinecap="round" />
                </svg>
                <input
                  ref={searchInputRef}
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  onBlur={() => {
                    if (!searchQuery) {
                      setSearchExpanded(false);
                    }
                  }}
                  placeholder="Search lists, tasks, notes..."
                  className="w-64 pl-10 pr-10 py-2 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                />
                {searchQuery && (
                  <button
                    onClick={() => {
                      setSearchQuery('');
                      setSearchExpanded(false);
                    }}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
                  >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                      <line x1="18" y1="6" x2="6" y2="18" />
                      <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                  </button>
                )}
              </div>
            ) : (
              <button
                onClick={() => {
                  setSearchExpanded(true);
                  setTimeout(() => searchInputRef.current?.focus(), 0);
                }}
                className="p-2 text-gray-400 hover:text-gray-600 cursor-pointer"
                title="Search"
              >
                <svg 
                  className="w-5 h-5" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <circle cx="11" cy="11" r="8" strokeWidth="2" />
                  <path d="M21 21l-4.35-4.35" strokeWidth="2" strokeLinecap="round" />
                </svg>
              </button>
            )}
          </div>

          <button
            onClick={() => setShowTaskOptions(!showTaskOptions)}
            className="text-xs text-gray-400 hover:text-gray-600 cursor-pointer whitespace-nowrap"
          >
            {showTaskOptions ? 'hide task options' : 'show task options'}
          </button>
        </div>

        {searchQuery && (
          <div className="mb-2 flex-shrink-0 text-sm text-gray-500">
            Found {incompleteLists.length + completedLists.length} list{incompleteLists.length + completedLists.length !== 1 ? 's' : ''}
          </div>
        )}

        {/* Mobile/Tablet: toggle button */}
        <div className="lg:hidden mb-4 flex-shrink-0">
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

        <div className="bg-white rounded-xl border border-gray-200 overflow-hidden flex-1 flex flex-col min-h-0">
          <div className="flex relative flex-1 overflow-hidden min-h-0">
            {/* Backdrop for mobile/tablet */}
            {mobileShowLists && (
              <div 
                className="absolute inset-0 bg-black/20 z-40 lg:hidden"
                onClick={() => setMobileShowLists(false)}
              />
            )}

            {/* Lists Sidebar - Slideover on mobile/tablet, normal on desktop */}
            <div className={`
              ${mobileShowLists ? 'translate-x-0' : '-translate-x-full'}
              lg:translate-x-0
              absolute lg:relative
              top-0 left-0
              bottom-0
              w-64
              bg-white
              transition-transform duration-300 ease-in-out
              z-50 lg:z-auto
              flex-shrink-0
              border-r border-gray-200 lg:border-r
              flex flex-col
            `}>
              <div className="p-4 overflow-y-auto flex-1">
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
            <div className="flex-1 min-w-0 p-4 sm:p-6 overflow-y-auto">
              {selectedList ? (
                <>
                  <div className="relative text-center mb-4">
                  {showTaskOptions && (
                    <button
                      onClick={() => setDeleteConfirmListId(selectedList.id)}
                      className="absolute right-0 top-0 text-gray-300 hover:text-red-500 cursor-pointer"
                      title="Delete list"
                    >
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                        <polyline points="3,6 5,6 21,6" />
                        <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6" />
                      </svg>
                    </button>
                  )}
                  <InlineEdit
                    value={selectedList.name}
                    onSave={(name) => handleRenameList(selectedList.id, name)}
                    className="text-lg sm:text-xl text-gray-600 tracking-wider break-words px-8"
                    tag="h2"
                    inputClassName="text-lg sm:text-xl text-gray-600 text-center tracking-wider w-full"
                  />
                </div>

                <form onSubmit={handleAddTask} className="flex flex-col sm:flex-row gap-3 mb-4">
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

                <hr className="mx-4 sm:mx-8 my-3 border-gray-200" />

                <TaskListView
                  tasks={selectedList.tasks || []}
                  showOptions={showTaskOptions}
                  searchQuery={searchQuery}
                  onToggle={handleToggleTask}
                  onDelete={handleDeleteTask}
                  onRename={handleRenameTask}
                  onUpdateNotes={handleUpdateTaskNotes}
                  onReorder={(taskIds) => handleTaskReorder(selectedList.id, taskIds)}
                />
              </>
            ) : (
              <div className="p-6 text-center text-gray-400">
                {(project.task_lists || []).length === 0
                  ? 'Create a list to get started'
                  : 'Select a list to view tasks'}
              </div>
            )}
            </div>
          </div>
        </div>
      </div>
      </div>

      {/* Delete Confirmation Modal */}
      {deleteConfirmListId && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl p-6 max-w-sm w-full shadow-xl">
            <h3 className="text-lg font-semibold text-gray-800 mb-2">Delete List?</h3>
            <p className="text-gray-600 text-sm mb-6">
              Are you sure you want to delete this list? All tasks in this list will be permanently deleted.
            </p>
            <div className="flex gap-3 justify-end">
              <button
                onClick={() => setDeleteConfirmListId(null)}
                className="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer"
              >
                Cancel
              </button>
              <button
                onClick={() => {
                  if (deleteConfirmListId) {
                    handleDeleteList(deleteConfirmListId);
                    setDeleteConfirmListId(null);
                  }
                }}
                className="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors cursor-pointer"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      )}
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

function TaskListView({ tasks, showOptions, searchQuery, onToggle, onDelete, onRename, onUpdateNotes, onReorder }: {
  tasks: Task[];
  showOptions: boolean;
  searchQuery?: string;
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
        searchQuery={searchQuery}
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
                searchQuery={searchQuery}
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

function DraggableTaskList({ tasks, showOptions, searchQuery, onToggle, onDelete, onRename, onUpdateNotes, onReorder, allTasks }: {
  tasks: Task[];
  showOptions: boolean;
  searchQuery?: string;
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
            searchQuery={searchQuery}
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

function TaskItem({ task, showOptions, searchQuery, onToggle, onDelete, onRename, onUpdateNotes }: {
  task: Task;
  showOptions: boolean;
  searchQuery?: string;
  onToggle: () => void;
  onDelete: () => void;
  onRename: (name: string) => void;
  onUpdateNotes: (notes: string) => void;
}) {
  const [expanded, setExpanded] = useState(false);
  const [notesValue, setNotesValue] = useState(task.notes || '');
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    setNotesValue(task.notes || '');
  }, [task.notes]);

  const saveNotes = async () => {
    if (notesValue !== (task.notes || '')) {
      setSaving(true);
      await onUpdateNotes(notesValue);
      setTimeout(() => setSaving(false), 500);
    }
  };

  const handleNotesBlur = () => {
    saveNotes();
  };

  const handleNotesKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) {
      e.preventDefault();
      saveNotes();
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

      {expanded && (
        <div className="ml-8 mr-2 mt-3 mb-2">
          <div className="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <label className="block text-xs font-medium text-gray-500 mb-2">
              NOTES
            </label>
            <div className="relative">
              <textarea
                value={notesValue}
                onChange={(e) => setNotesValue(e.target.value)}
                onBlur={handleNotesBlur}
                onKeyDown={handleNotesKeyDown}
                placeholder="Add notes here... (⌘+Enter to save)"
                className="w-full text-sm text-gray-700 bg-white border border-gray-200 rounded-md p-3 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 resize-none transition-colors"
                rows={4}
              />
              {saving && (
                <div className="absolute bottom-3 right-3 flex items-center gap-2 text-xs text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-200">
                  <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                  </svg>
                  Saved
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
