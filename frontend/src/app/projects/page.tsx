'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { useAuth } from '@/lib/auth';
import { api } from '@/lib/api';
import { Project } from '@/lib/types';
import Header from '@/components/Header';

export default function ProjectsPage() {
  const { user, loading } = useAuth();
  const router = useRouter();
  const [projects, setProjects] = useState<Project[]>([]);
  const [loadingProjects, setLoadingProjects] = useState(true);
  const [showDeleteModal, setShowDeleteModal] = useState<number | null>(null);
  const [showAddModal, setShowAddModal] = useState(false);
  const [newProjectName, setNewProjectName] = useState('');
  const [completedOpen, setCompletedOpen] = useState(false);

  useEffect(() => {
    if (!loading && !user) {
      router.replace('/login');
    }
  }, [user, loading, router]);

  const fetchProjects = useCallback(async () => {
    try {
      const data = await api.getProjects();
      setProjects(data);
    } catch {
      // ignore
    } finally {
      setLoadingProjects(false);
    }
  }, []);

  useEffect(() => {
    if (user) fetchProjects();
  }, [user, fetchProjects]);

  const handleCreateProject = async () => {
    if (!newProjectName.trim()) return;
    try {
      await api.createProject(newProjectName.trim());
      setNewProjectName('');
      setShowAddModal(false);
      fetchProjects();
    } catch {
      // ignore
    }
  };

  const handleDeleteProject = async (id: number) => {
    try {
      await api.deleteProject(id);
      setShowDeleteModal(null);
      fetchProjects();
    } catch {
      // ignore
    }
  };

  const incompleteProjects = projects.filter((p) => !p.completed);
  const completedProjects = projects.filter((p) => p.completed);

  if (loading || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-400" />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-6xl mx-auto px-6 py-8">
        <h1
          className="text-3xl text-center text-gray-700 mb-8"
          style={{ fontFamily: "'Julius Sans One', sans-serif" }}
        >
          Projects
        </h1>

        {loadingProjects ? (
          <div className="flex justify-center py-12">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-400" />
          </div>
        ) : (
          <div className="flex flex-col gap-6">
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              {incompleteProjects.map((project) => (
                <ProjectTile
                  key={project.id}
                  project={project}
                  onClick={() => router.push(`/projects/${project.id}`)}
                  onDelete={() => setShowDeleteModal(project.id)}
                />
              ))}
              <button
                onClick={() => { setShowAddModal(true); setNewProjectName(''); }}
                className="aspect-square rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-300 text-6xl hover:bg-gray-200 hover:text-gray-500 hover:border-gray-400 hover:border-solid transition-all cursor-pointer"
              >
                +
              </button>
            </div>

            {completedProjects.length > 0 && (
              <div>
                <button
                  onClick={() => setCompletedOpen(!completedOpen)}
                  className="flex items-center w-full mb-4 py-1 px-4 rounded-lg transition cursor-pointer hover:bg-gray-200"
                >
                  <hr className="w-8 border-gray-400" />
                  <span className="mx-3 text-gray-500 whitespace-nowrap text-sm">
                    Completed ({completedProjects.length})
                  </span>
                  <hr className="flex-1 border-gray-400" />
                  <svg
                    className={`ml-2 w-3 h-3 text-gray-400 transition-transform ${completedOpen ? 'rotate-90' : '-rotate-90'}`}
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
                {completedOpen && (
                  <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    {completedProjects.map((project) => (
                      <ProjectTile
                        key={project.id}
                        project={project}
                        completed
                        onClick={() => router.push(`/projects/${project.id}`)}
                        onDelete={() => setShowDeleteModal(project.id)}
                      />
                    ))}
                  </div>
                )}
              </div>
            )}
          </div>
        )}
      </div>

      {showAddModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="absolute inset-0 bg-gray-500 opacity-20" onClick={() => setShowAddModal(false)} />
          <div className="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-semibold text-gray-700">Create new project</h3>
              <button onClick={() => setShowAddModal(false)} className="text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                </svg>
              </button>
            </div>
            <form onSubmit={(e) => { e.preventDefault(); handleCreateProject(); }}>
              <input
                type="text"
                value={newProjectName}
                onChange={(e) => setNewProjectName(e.target.value)}
                placeholder="Project name"
                autoFocus
                className="w-full px-4 py-3 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 mb-4"
              />
              <button
                type="submit"
                className="w-full py-3 rounded-lg text-white font-semibold text-sm bg-gradient-to-r from-[#FFE000] to-[#FFC200] hover:opacity-90 transition-opacity cursor-pointer"
              >
                Create Project
              </button>
            </form>
          </div>
        </div>
      )}

      {showDeleteModal !== null && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="absolute inset-0 bg-gray-500 opacity-20" onClick={() => setShowDeleteModal(null)} />
          <div className="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-semibold text-gray-700">Are you sure you want to delete? This is permanent.</h3>
              <button onClick={() => setShowDeleteModal(null)} className="text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                </svg>
              </button>
            </div>
            <button
              onClick={() => handleDeleteProject(showDeleteModal)}
              className="w-full py-3 rounded-lg text-white font-semibold text-sm bg-red-600 hover:bg-red-700 transition-colors cursor-pointer"
            >
              Yes, delete
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

function ProjectTile({ project, completed, onClick, onDelete }: {
  project: Project;
  completed?: boolean;
  onClick: () => void;
  onDelete: () => void;
}) {
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <div className="relative group">
      <button
        onClick={onClick}
        className={`w-full aspect-square rounded-2xl border-2 border-gray-300 flex flex-col items-center justify-center p-3 text-center hover:bg-gray-200 transition-all cursor-pointer relative ${completed ? 'text-gray-400' : 'text-gray-500'}`}
      >
        {completed && (
          <div className="absolute left-2 top-2 h-7 w-7 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
            <svg width="14" height="10" viewBox="0 0 14 10" fill="none">
              <path d="M1 5L5 9L13 1" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </div>
        )}
        <span className={`text-xl line-clamp-3 ${completed ? 'line-through' : ''}`} style={{ fontFamily: "'Poppins', sans-serif" }}>
          {project.name}
        </span>
      </button>
      <div className={`text-xs text-gray-400 flex justify-between px-1 mt-1 ${completed ? 'line-through' : ''}`}>
        <span>Lists: {project.total_lists}</span>
        <span>Tasks: {project.completed_tasks}/{project.total_tasks}</span>
      </div>

      <div
        onClick={(e) => { e.stopPropagation(); setMenuOpen(!menuOpen); }}
        className="absolute top-3 right-2 hover:bg-gray-300 w-7 h-7 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-50 hover:!opacity-100 z-10 cursor-pointer"
      >
        <svg width="4" height="16" viewBox="0 0 6 20" fill="none">
          <circle cx="3" cy="3" r="2.5" fill="#64748b" />
          <circle cx="3" cy="10" r="2.5" fill="#64748b" />
          <circle cx="3" cy="17" r="2.5" fill="#64748b" />
        </svg>
      </div>
      {menuOpen && (
        <>
          <div className="fixed inset-0 z-20" onClick={() => setMenuOpen(false)} />
          <div className="absolute z-30 rounded-lg shadow-lg right-2 top-12">
            <button
              onClick={(e) => { e.stopPropagation(); setMenuOpen(false); onDelete(); }}
              className="rounded-lg bg-white px-4 py-3 border border-gray-200 hover:bg-gray-100 text-sm cursor-pointer whitespace-nowrap"
            >
              Delete
            </button>
          </div>
        </>
      )}
    </div>
  );
}
