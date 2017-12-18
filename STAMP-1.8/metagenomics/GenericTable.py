from PyQt4 import QtCore
import operator

from metagenomics.TableHelper import SortTableStrCol
from metagenomics.StringHelper import isNumber
 
class GenericTable(QtCore.QAbstractTableModel): 
  def __init__(self, data, headers, parent=None, *args): 
    QtCore.QAbstractTableModel.__init__(self, parent, *args) 
    self.arraydata = data
    self.headerdata = headers
  
  def rowCount(self, parent): 
    return len(self.arraydata) 
  
  def columnCount(self, parent): 
    if len(self.arraydata) > 0:
      return len(self.arraydata[0]) 
    else:
      return -1
  
  def data(self, index, role): 
    if not index.isValid(): 
        return QtCore.QVariant() 
    elif role != QtCore.Qt.DisplayRole: 
        return QtCore.QVariant() 
    return QtCore.QVariant(self.arraydata[index.row()][index.column()]) 
  
  def headerData(self, col, orientation, role):
    if orientation == QtCore.Qt.Horizontal and role == QtCore.Qt.DisplayRole:
        return QtCore.QVariant(self.headerdata[col])
    return QtCore.QVariant()
  
  def sort(self, Ncol, order):
    '''
    Sort table by given column number.
    '''
    if len(self.arraydata) == 0:
      return
    
    self.emit(QtCore.SIGNAL("layoutAboutToBeChanged()"))
      
    dataIsNumeric = isNumber(self.arraydata[0][Ncol])
    
    if dataIsNumeric:
      self.arraydata = sorted(self.arraydata , key = operator.itemgetter(Ncol))
    else:
      self.arraydata = SortTableStrCol(self.arraydata, Ncol)      
        
    if order == QtCore.Qt.DescendingOrder:
        self.arraydata.reverse()
    self.emit(QtCore.SIGNAL("layoutChanged()"))
    
  def save(self, filename):
    fout = open(filename, 'w')
    for header in self.headerdata:
      fout.write(str(header) + '\t')
    fout.write('\n')
      
    for row in self.arraydata:
      for item in row:
        fout.write(str(item) + '\t')
      fout.write('\n')
      
    fout.close()
      

