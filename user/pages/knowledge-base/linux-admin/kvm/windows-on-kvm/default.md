---
visible: true
title: Windows Guests on KVM
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [kvm]
---

Source:   [RedHat](https://access.redhat.com/articles/2470791)

Give the vm 2 vcpus and at least 4g ram.  ( The new install will take up 10G of disk space. ) allocate whatever is needed above that.

When building the vm choose virtio for disk and network drivers.

Make the following 2 changes to the XML before installing:
```
virsh edit <vmname>

  <clock offset='localtime'>
    <timer name='hypervclock' present='yes'/>
  </clock>
```

And up the video memory:
```
<video>
	  <model type='qxl' ram='65536' vram='65536' vgamem='65536' heads='1' primary='yes'/>
	  <address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
	</video>
```

Get the Windows virtio drivers
```
wget https://fedorapeople.org/groups/virt/virtio-win/direct-downloads/archive-virtio/virtio-win-0.1.141-1/virtio-win.iso
```

Put the iso file in the ISO folder on the host.

Attach 2 IDE cdroms to the vm, and point the second one to the virtio-win.iso from above.

During install it will not find the disk drivers and will prompt you. Browse on the 2d cdrom and find the _viostor_ folder and choose the _win10_ driver.

After install you can go to device manager and install the following:

video  — will be a HUGE improvement
balloon
serial

NOTE:  Go to the properties then details and under hardware ids you will see codes to point you to the right driver for the given device.

- VEN_1AF4&DEV_1002 or VEN_1AF4&DEV_1045, the balloon device.
- VEN_1AF4&DEV_1003 or VEN_1AF4&DEV_1043, the paravirtual serial port device.
- VEN_1AF4&DEV_1000 or VEN_1AF4&DEV_1041, the network device.
- VEN_1AF4&DEV_1001 or VEN_1AF4&DEV_1042, the block device.
- VEN_1AF4&DEV_1004 or VEN_1AF4&DEV_1048, the SCSI block device.
- VEN_1AF4&DEV_1005 or VEN_1AF4&DEV_1044, the entropy source device.
- VEN_1B36&DEV_0002, the emulated PCI serial driver.
- VEN_1B36&DEV_0100, the video device.
- VEN_QEMU&DEV_0001, the guest panic device.

!! After install the CPUs will be pegged because of the OneDrive setup going on. Disable it.

Install Windows Guest Tools from [spice](https://www.spice-space.org/download/windows/spice-guest-tools/spice-guest-tools-latest.exe)

#### Create Windows 10 template

edit the existing win10 domain
```
virsh edit win10
```

Change Name, UUID, MAC, disk name (set to anything or newly created).

Save to desired name of new vm

Open the new vm in virt-manager and create new disk if applicable
