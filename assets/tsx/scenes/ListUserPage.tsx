import React from "react";
import {Route, Routes, Link, } from 'react-router-dom';
import Table from '@mui/material/Table';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableBody from '@mui/material/TableBody';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';

import { ListUserLine } from "@/pods/users/components/list-user-line";


export interface UserEntity {
    id: string;
    email: string;
  }

export const ListUserPage: React.FC = () => {

    const [users, setUsers] = React.useState<UserEntity[]>([]);

    React.useEffect(() => {
        fetch(`https://localhost/getalluser`)
          .then((response) => (response.ok) ? (response.json()) : ([]))
          .then((json) => setUsers(json));
        },
        []);

    const showUsers = (users) => {
        return (!users.length) 
              ? (<><TableRow><TableCell align = "left"><h3>Ningún usuario...</h3></TableCell></TableRow></>)
              : users.map((user) => <ListUserLine key = {user.id} user = {user}/>);
         } 

    return (
        <>
        <h1>Takion users</h1>
        
        <br></br><br></br>
        <label>Nota: Esto es una pagina de prueba para emitir un listado completo de usuarios. </label>
              
        <TableContainer>
            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell align="left">Id</TableCell>
                        <TableCell align="left">Email</TableCell>
                    </TableRow>
                </TableHead>      
                <TableBody>
                    {showUsers(users)}
                </TableBody>
            </Table>
        </TableContainer>
        </>);
}


