import React from 'react';
import LoginForm from '../../../LoginForm';
import FooterLogin from './FooterLogin';
import '../styles/app.css';

const HomePage = () => {
    return (
        <div className="app-container">
            <div className="login-form">
                <LoginForm />
            </div>
            <FooterLogin /> 
        </div>
    );
};

export default HomePage;