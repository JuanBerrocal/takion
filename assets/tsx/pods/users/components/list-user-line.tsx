import React from 'react';
import { Link, generatePath } from "react-router-dom";
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';

import {UserEntity} from '@/scenes//ListUserPage';

interface Props {
  user: UserEntity;
}

export const ListUserLine: React.FC<Props> = (props) => {

  const {user} = props;

  return <>
      <TableRow>
        
        <TableCell align="left">
          <span>{user.id}</span>
        </TableCell>
        <TableCell align="left">
            {user.email}
        </TableCell>
      </TableRow>
    </>;
}