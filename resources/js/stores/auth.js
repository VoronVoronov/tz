import { ref } from 'vue'

const user = ref(null)
const token = ref(localStorage.getItem('token'))

export function useAuth() {
    const login = async (email, password) => {
        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password }),
            })
            
            if (!response.ok) {
                throw new Error('Login failed')
            }

            const data = await response.json()
            token.value = data.token
            localStorage.setItem('token', data.token)
            await fetchUser()
            
            return true
        } catch (error) {
            console.error('Login error:', error)
            return false
        }
    }

    const logout = () => {
        user.value = null
        token.value = null
        localStorage.removeItem('token')
    }

    const fetchUser = async () => {
        if (!token.value) return null

        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token.value}`
                }
            })
            
            if (!response.ok) {
                throw new Error('Failed to fetch user')
            }

            const data = await response.json()
            user.value = data
            return data
        } catch (error) {
            console.error('Fetch user error:', error)
            logout()
            return null
        }
    }

    const isAuthenticated = () => !!token.value

    return {
        user,
        token,
        login,
        logout,
        fetchUser,
        isAuthenticated
    }
}
