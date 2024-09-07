import axios from 'axios';

class ApiRequestManager {
 
    getToken = () => {
        
        return localStorage.getItem('authToken');
    }

 
    setToken = (newToken) => {
        localStorage.setItem('authToken', newToken);
    }

 
    handleAuthError = (error) => {
        if (error.response && error.response.status === 401) {
             
            window.location.href = '/';
            return
        } else {
            
            throw error.response.data.error?error.response.data.error:'error al hacer la peticion'
             

        }
    }

    postMethod = async (url, payload) => {
        const token = this.getToken();

        try {
            const response = await axios.post(url, payload, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });

 
            const newToken = response.headers['authorization'];
            if (newToken && newToken !== `Bearer ${token}`) {
 
                this.setToken(newToken.replace('Bearer ', ''));
            }

             
            return response.data;

        } catch (error) {
             
            return this.handleAuthError(error);
        }
    }

     
    getMethod = async (url) => {
        const token = this.getToken();
 

        try {
            const response = await axios.get(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });

             
            const newToken = response.headers['authorization'];
            if (newToken && newToken !== `Bearer ${token}`) {
                 
                this.setToken(newToken.replace('Bearer ', ''));
            }

             
            return response.data;

        } catch (error) {
             
            return this.handleAuthError(error);
        }
    }
    deleteMethod=async (url)=>{
        const token = this.getToken();
        try {
            const response = await axios.delete(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });
            const newToken = response.headers['authorization'];
            if (newToken && newToken !== `Bearer ${token}`) {
                 
                this.setToken(newToken.replace('Bearer ', ''));
            }
            return response.data;
        } catch (error) {
            return this.handleAuthError(error);
        }
    }
}

export default ApiRequestManager;
