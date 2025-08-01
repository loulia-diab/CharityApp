import React from 'react'
import SearchIcon from '@mui/icons-material/Search';
import LanguageIcon from '@mui/icons-material/Language';
import Switch from '@mui/material/Switch';
import "./navbar.scss"

const Navbar = () => {
  return (
    <div className='navbar'>
      <div className="navbarContainer">
        <div className="search">
          <input type="text" placeholder="search" />      
              <SearchIcon  className='icon'/>
          </div>
        <div className="items">
          <div className="item">
          <LanguageIcon className='icon'/>
          <span>
                English
              </span>
          </div>
          <div className="item">
                 <Switch style={{color: "black"}} className='icon'/>
            </div>
            <div className="item">
              <img src="/assets/person.jpg" alt="" className='profileimg'/>
            </div>
        </div>
      </div>
      </div>
  )
}

export default Navbar