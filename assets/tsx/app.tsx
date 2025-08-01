/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.css';
import React from "react";
import ReactDOM from 'react-dom';
import {BrowserRouter as Router, Routes, Route} from 'react-router-dom';
import {RouterComponent} from '@/core/router';


export const App = () => {
      return <RouterComponent />;
      
}

ReactDOM.render(
      <div>
        <App />
      </div>,
      document.getElementById("root")
    );