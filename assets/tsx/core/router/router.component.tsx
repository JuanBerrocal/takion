import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import {HomePage} from "@/scenes/HomePage";
import {ListUserPage} from "@/scenes/ListUserPage";

export const RouterComponent: React.FC = () => {
    return (
        <Router>
            <Routes>
                <Route path="/home" element = {<HomePage />} />
                <Route path="/getalluser" element = {<ListUserPage />} />
            </Routes>
        </Router>
    );
}