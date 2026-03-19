import { useState, useEffect } from 'react'

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000'

function getToken() {
  if (typeof window !== 'undefined') return localStorage.getItem('token') || ''
  return ''
}

function headers() {
  return { 'Content-Type': 'application/json', Authorization: `Bearer ${getToken()}` }
}

export interface DataSource {
  id: number
  id: number
  name: string
  connection_string: string
  type: string
  user_id: number
  created_at: string
  updated_at: string
}

export function useDataSources() {
  const [items, setItems] = useState<DataSource[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  async function getAll() {
    setLoading(true)
    try {
      const res = await fetch(`${API_URL}/api/v1/data_sources`, { headers: headers() })
      const data = await res.json()
      setItems(data.data || data)
    } catch (e: any) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }

  async function create(payload: Partial<DataSource>) {
    const res = await fetch(`${API_URL}/api/v1/data_sources`, {
      method: 'POST', headers: headers(), body: JSON.stringify(payload),
    })
    if (!res.ok) throw new Error('Failed to create')
    await getAll()
  }

  async function update(id: number, payload: Partial<DataSource>) {
    const res = await fetch(`${API_URL}/api/v1/data_sources/${id}`, {
      method: 'PUT', headers: headers(), body: JSON.stringify(payload),
    })
    if (!res.ok) throw new Error('Failed to update')
    await getAll()
  }

  async function remove(id: number) {
    const res = await fetch(`${API_URL}/api/v1/data_sources/${id}`, {
      method: 'DELETE', headers: headers(),
    })
    if (!res.ok) throw new Error('Failed to delete')
    await getAll()
  }

  useEffect(() => { getAll() }, [])

  return { items, loading, error, getAll, create, update, remove }
}
