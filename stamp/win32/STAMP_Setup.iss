; Script generated by the Inno Setup Script Wizard.
; SEE THE DOCUMENTATION FOR DETAILS ON CREATING INNO SETUP SCRIPT FILES!

[Setup]
; NOTE: The value of AppId uniquely identifies this application.
; Do not use the same AppId value in installers for other applications.
; (To generate a new GUID, click Tools | Generate GUID inside the IDE.)
AppId={{058AA4B2-FB5A-4D89-9134-D6E06AB9E894}
AppName=STAMP
AppVerName=STAMP 1.08
AppPublisher=Donovan Parks and Robert Beiko
AppPublisherURL=http://kiwi.cs.dal.ca/Software/STAMP
AppSupportURL=http://kiwi.cs.dal.ca/Software/STAMP
AppUpdatesURL=http://kiwi.cs.dal.ca/Software/STAMP
DefaultDirName={pf}\STAMP
DefaultGroupName=STAMP
AllowNoIcons=yes
LicenseFile=D:\PhD\Development\Projects\STAMP\dist\license.txt
InfoBeforeFile=D:\PhD\Development\Projects\STAMP\dist\readme.txt
OutputDir=D:\PhD\Development\Projects\STAMP\win32\install
OutputBaseFilename=STAMP_1.08_Setup
SetupIconFile=D:\PhD\Development\Projects\STAMP\dist\icons\programIcon.ico
Compression=lzma
SolidCompression=yes

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked

[Files]
Source: "D:\PhD\Development\Projects\STAMP\dist\STAMP.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\STAMP.exe.log"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\commandLine.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\license.txt"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\python26.dll"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\STAMP_Users_Guide.pdf"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\w9xpopen.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\PhD\Development\Projects\STAMP\dist\data\*"; DestDir: "{app}\data"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "D:\PhD\Development\Projects\STAMP\dist\examples\*"; DestDir: "{app}\examples"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "D:\PhD\Development\Projects\STAMP\dist\icons\*"; DestDir: "{app}\icons"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "D:\PhD\Development\Projects\STAMP\dist\library\*"; DestDir: "{app}\library"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "D:\PhD\Development\Projects\STAMP\dist\mpl-data\*"; DestDir: "{app}\mpl-data"; Flags: ignoreversion recursesubdirs createallsubdirs
; NOTE: Don't use "Flags: ignoreversion" on any shared system files

[Icons]
Name: "{group}\STAMP"; Filename: "{app}\STAMP.exe"
Name: "{group}\{cm:ProgramOnTheWeb,STAMP}"; Filename: "http://kiwi.cs.dal.ca/Software/STAMP"
Name: "{group}\{cm:UninstallProgram,STAMP}"; Filename: "{uninstallexe}"
Name: "{commondesktop}\STAMP"; Filename: "{app}\STAMP.exe"; Tasks: desktopicon

[Run]
Filename: "{app}\STAMP.exe"; Description: "{cm:LaunchProgram,STAMP}"; Flags: nowait postinstall skipifsilent

