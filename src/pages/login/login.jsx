import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './login.scss'

const login = () => {
  return (
    <div>
        <Sidebar />
       <div className="volunteerContainer">
        <Navbar />
        <Filter/>
      </div>
    </div>
  )
}

export default login