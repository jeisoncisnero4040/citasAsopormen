import React from 'react';
import LoginForm from './LoginForm';
import FooterLogin from './FooterLogin';
import '../styles/app.css';

const HomePage = () => {
    return (
        <div className="app-container">
            <div className="left"></div>
            <div className="login-form">
                <LoginForm />
            </div>
            <div className="right"></div>
            <FooterLogin /> 
        </div>
    );
};

export default HomePage;