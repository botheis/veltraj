Veltraj
===

Author : Botheis

# What is veltraj ?

Veltraj is a bikes manager for personnal usage. The purpose of this manager is to be able to add bikes, describes its embedded accessories and gives some monitoring info.

# Behaviours

Status:
- [x] available functionnalities
- [ ] to be developped functionnalities

There is a list of functionnalities, which defines the goals of the project. The functionnalities around the tracker need a mobile app (need to be developed too).

- Bikes:
  - [ ] List the bikes
  - [ ] Add a bike
  - [ ] Modify a bike
  - [ ] Delete a bike

- Bikes Accessories:
  - [ ] Find default accessories for each bike
  - [ ] Create a catalogue of accessories stored
  - [ ] List the accessories
  - [ ] Add a accessory
  - [ ] Modify an accessory
  - [ ] Delete an accessory
  - [ ] Modify bikes accessories
  - [ ] Give compatibilities between bikes and accessories
  - [ ] Find better compatible accessories

- Trails Maps and Plannifications
  - [ ] Planify a trail map
  - [ ] Extract statistics from the planified trails
  - [ ] Tells which bike suits better the plannified trail (if several bikes are registered).
  - [ ] Track the trail progression
  - [ ] Extract statistics from the trails progressions

- Maintenance
  - [ ] Gives bikes maintenance infos based on the trails progression
  - [ ] For each bikes and accessories gives the tools needed to do the maintenance
  - [ ] Create a catalogue of tools

- Extra tools
  - [ ] Catalogue of extra tools


# Requirements

- debian 12

# How to install

First you have to clone the repository:
      
    git clone git@github.com:botheis/veltraj.git

Then you have to run the setup.sh script:

    sudo /path/to/veltraj/setup.sh

Theorically it should do all the job.